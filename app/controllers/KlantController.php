<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\KlantModel;

/**
 * CRUD-beheer voor klanten (alleen medewerker/eigenaar).
 */
class KlantController extends Controller
{
    private KlantModel $klantModel;

    public function __construct()
    {
        parent::__construct();
        $this->klantModel = new KlantModel();
    }

    // ----------------------------------------------------------------
    // GET /klanten
    // ----------------------------------------------------------------
    public function index(): void
    {
        $this->vereisLogin();
        $flash = $this->getFlash();

        // Postcode filter
        $gezochtPostcode = trim($_GET['postcode'] ?? '');
        $filterActief    = $gezochtPostcode !== '';
        $postcode        = $filterActief ? $gezochtPostcode : null;

        // Paginering – 4 klanten per pagina (wireframe toont 4 rijen)
        $perPagina     = 4;
        $huidigePagina = max(1, (int)($_GET['pagina'] ?? 1));

        $totaalKlanten = $this->klantModel->telKlanten($postcode);
        $totaalPaginas = max(1, (int)ceil($totaalKlanten / $perPagina));
        $huidigePagina = min($huidigePagina, $totaalPaginas);
        $offset        = ($huidigePagina - 1) * $perPagina;

        $klanten       = $this->klantModel->haalKlantenOp($postcode, $perPagina, $offset);

        // Log de actie
        $this->klantModel->logTechnischeActie(
            'INFO',
            'KlantController',
            'Klanten overzicht bekeken',
            json_encode([
                'postcode' => $postcode ?? 'alle',
                'pagina'   => $huidigePagina,
                'totaal'   => $totaalKlanten
            ])
        );

        $this->view('klanten/index', compact(
            'flash',
            'klanten',
            'huidigePagina',
            'totaalPaginas',
            'totaalKlanten',
            'gezochtPostcode',
            'filterActief'
        ));
    }

    // ----------------------------------------------------------------
    // GET /klanten/detail
    // ----------------------------------------------------------------
    public function detail(): void
    {
        $this->vereisLogin();
        $id    = (int) ($_GET['id'] ?? 0);
        $klant = $this->klantModel->vindOpId($id);

        if (!$klant) {
            $this->setFlash('error', 'Klant niet gevonden.');
            $this->redirect('/klanten');
        }

        $this->view('klanten/detail', compact('klant'));
    }

    // ----------------------------------------------------------------
    // GET /klanten/aanmaken
    // ----------------------------------------------------------------
    public function aanmakenForm(): void
    {
        $this->vereisLogin();
        $csrfToken = $this->genereerCsrfToken();
        $flash     = $this->getFlash();
        $oud       = [];

        $this->view('klanten/aanmaken', compact('csrfToken', 'flash', 'oud'));
    }

    // ----------------------------------------------------------------
    // POST /klanten/aanmaken  (stub – uitbreidbaar)
    // ----------------------------------------------------------------
    public function aanmaken(): void
    {
        $this->vereisLogin();
        $this->setFlash('success', 'Klant aangemaakt.');
        $this->redirect('/klanten');
    }

    // ----------------------------------------------------------------
    // GET /klanten/wijzigen
    // ----------------------------------------------------------------
    public function wijzigenForm(): void
    {
        $this->vereisLogin();
        $id    = (int) ($_GET['id'] ?? 0);
        $klant = $this->klantModel->vindOpId($id);

        if (!$klant) {
            $this->setFlash('error', 'Klant niet gevonden.');
            $this->redirect('/klanten');
        }

        $csrfToken = $this->genereerCsrfToken();
        $flash     = $this->getFlash();

        $this->view('klanten/wijzigen', compact('csrfToken', 'flash', 'klant'));
    }

    // ----------------------------------------------------------------
    // POST /klanten/wijzigen
    // ----------------------------------------------------------------
    public function wijzigen(): void
    {
        $this->vereisLogin();

        // CSRF validatie
        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldig CSRF-token.');
            $this->redirect('/klanten');
        }

        $id              = (int) ($_POST['id'] ?? 0);
        $contactEmail    = trim($_POST['contact_email'] ?? '');
        $straatnaam      = trim($_POST['straatnaam'] ?? '');
        $huisnummer      = trim($_POST['huisnummer'] ?? '');
        $toevoeging      = trim($_POST['toevoeging'] ?? '');
        $postcode        = trim($_POST['postcode'] ?? '');
        $plaats          = trim($_POST['plaats'] ?? '');
        $mobiel          = trim($_POST['mobiel'] ?? '');
        $bijzonderheden  = trim($_POST['bijzonderheden'] ?? '');

        // Haal klant op
        $klant = $this->klantModel->vindOpId($id);
        if (!$klant) {
            $this->setFlash('error', 'Klant niet gevonden.');
            $this->redirect('/klanten');
        }

        // Haal het gekoppelde ContactId op
        $contactId = $this->klantModel->getContactIdVoorKlant($id);
        if (!$contactId) {
            $this->setFlash('error', 'Contactgegevens niet gevonden.');
            $this->redirect('/klanten/detail?id=' . $id);
        }

        // Serverside validatie
        $validatieErrors = [];

        if ($f = Validator::foutEmail($contactEmail, 'Contact e-mailadres')) {
            $validatieErrors['contact_email'] = $f;
        }
        if ($f = Validator::foutVerplicht($straatnaam, 'Straatnaam')) {
            $validatieErrors['straatnaam'] = $f;
        }
        if ($f = Validator::foutHuisnummer($huisnummer)) {
            $validatieErrors['huisnummer'] = $f;
        }
        if ($f = Validator::foutPostcode($postcode)) {
            $validatieErrors['postcode'] = $f;
        }
        if ($f = Validator::foutPlaats($plaats)) {
            $validatieErrors['plaats'] = $f;
        }
        if ($f = Validator::foutVerplicht($mobiel, 'Mobiel')) {
            $validatieErrors['mobiel'] = $f;
        }

        if (!empty($validatieErrors)) {
            $this->klantModel->logTechnischeActie(
                'WARNING',
                'KlantController',
                'Klant wijzigen mislukt (validatie)',
                json_encode(['klant_id' => $id, 'errors' => $validatieErrors])
            );

            $csrfToken = $this->genereerCsrfToken();
            $flash     = [
                'type'    => 'error',
                'bericht' => 'Klantgegevens zijn niet bijgewerkt',
                'errors'  => $validatieErrors
            ];

            // Overschrijf klantdata met ingevoerde waarden
            $klant['Email']          = $contactEmail;
            $klant['Straatnaam']     = $straatnaam;
            $klant['Huisnummer']     = $huisnummer;
            $klant['Toevoeging']     = $toevoeging;
            $klant['Postcode']       = $postcode;
            $klant['Plaats']         = $plaats;
            $klant['Mobiel']         = $mobiel;
            $klant['Bijzonderheden'] = $bijzonderheden;

            $this->view('klanten/wijzigen', compact('csrfToken', 'flash', 'klant'));
            return;
        }

        // Valideer email uniciteit (uitgezonderd de huidige contact)
        if ($this->klantModel->emailBestaatAl($contactEmail, $contactId)) {
            $this->klantModel->logTechnischeActie(
                'WARNING',
                'KlantController',
                'Poging tot wijzigen met bestaand e-mailadres',
                json_encode(['klant_id' => $id, 'email' => $contactEmail])
            );

            $csrfToken = $this->genereerCsrfToken();
            $flash     = [
                'type'    => 'error',
                'bericht' => 'Klantgegevens zijn niet bijgewerkt',
                'errors'  => ['contact_email' => 'Het e-mailadres is al in gebruik']
            ];

            // Refresh klantgegevens met posted waarden voor display
            $klant['Email']          = $contactEmail;
            $klant['Straatnaam']     = $straatnaam;
            $klant['Huisnummer']     = $huisnummer;
            $klant['Toevoeging']     = $toevoeging;
            $klant['Postcode']       = $postcode;
            $klant['Plaats']         = $plaats;
            $klant['Mobiel']         = $mobiel;
            $klant['Bijzonderheden'] = $bijzonderheden;

            $this->view('klanten/wijzigen', compact('csrfToken', 'flash', 'klant'));
            return;
        }

        // Voer de wijziging door
        $this->klantModel->wijzigKlant($id, [
            'contact_email'  => $contactEmail,
            'straatnaam'     => $straatnaam,
            'huisnummer'     => $huisnummer,
            'toevoeging'     => $toevoeging,
            'postcode'       => $postcode,
            'plaats'         => $plaats,
            'mobiel'         => $mobiel,
            'bijzonderheden' => $bijzonderheden,
        ]);

        // Technische log
        $this->klantModel->logTechnischeActie(
            'INFO',
            'KlantController',
            'Klantgegevens bijgewerkt',
            json_encode([
                'klant_id'    => $id,
                'velden'      => ['contact_email', 'straatnaam', 'huisnummer', 'postcode', 'plaats', 'mobiel'],
            ])
        );

        $this->setFlash('success', 'Klantgegevens bijgewerkt');
        $this->redirect('/klanten');
    }

    // ----------------------------------------------------------------
    // POST /klanten/verwijderen  (stub)
    // ----------------------------------------------------------------
    public function verwijderen(): void
    {
        $this->vereisLogin();
        $this->setFlash('success', 'Klant verwijderd.');
        $this->redirect('/klanten');
    }
}
