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
        $voornaam        = trim($_POST['voornaam'] ?? '');
        $tussenvoegsel   = trim($_POST['tussenvoegsel'] ?? '');
        $achternaam      = trim($_POST['achternaam'] ?? '');
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

        if ($f = Validator::foutNaam($voornaam, 'Voornaam')) {
            $validatieErrors['voornaam'] = $f;
        }
        if (!empty($tussenvoegsel) && $f = Validator::foutNaam($tussenvoegsel, 'Tussenvoegsel')) {
            $validatieErrors['tussenvoegsel'] = $f;
        }
        if ($f = Validator::foutNaam($achternaam, 'Achternaam')) {
            $validatieErrors['achternaam'] = $f;
        }
        if (!empty($contactEmail) && $f = Validator::foutEmail($contactEmail, 'Contact e-mailadres')) {
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
        if ($f = Validator::foutTelefoonnummer($mobiel, 'Mobiel')) {
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
            $klant['Voornaam']       = $voornaam;
            $klant['Tussenvoegsel']  = $tussenvoegsel;
            $klant['Achternaam']     = $achternaam;
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

        // ── Email uniciteit en opslaan worden afgehandeld door de stored procedure ──
        // Voer de wijziging door via stored procedure
        $resultaat = $this->klantModel->wijzigKlant($id, [
            'voornaam'       => $voornaam,
            'tussenvoegsel'  => $tussenvoegsel,
            'achternaam'     => $achternaam,
            'contact_email'  => $contactEmail,
            'straatnaam'     => $straatnaam,
            'huisnummer'     => $huisnummer,
            'toevoeging'     => $toevoeging,
            'postcode'       => $postcode,
            'plaats'         => $plaats,
            'mobiel'         => $mobiel,
            'bijzonderheden' => $bijzonderheden,
        ]);

        // Stored procedure kan ook email-conflict terugmelden
        if (!$resultaat['success']) {
            $this->klantModel->logTechnischeActie(
                'WARNING', 'KlantController',
                'Klant wijzigen geweigerd door stored procedure',
                json_encode(['klant_id' => $id, 'message' => $resultaat['message']])
            );

            $csrfToken = $this->genereerCsrfToken();
            $flash     = [
                'type'    => 'error',
                'bericht' => 'Klantgegevens zijn niet bijgewerkt',
                'errors'  => ['contact_email' => $resultaat['message']],
            ];

            $klant['Voornaam']       = $voornaam;
            $klant['Tussenvoegsel']  = $tussenvoegsel;
            $klant['Achternaam']     = $achternaam;
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
