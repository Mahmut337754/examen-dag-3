<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\MedewerkerModel;

/**
 * Controller voor medewerkersoverzicht en -beheer.
 */
class MedewerkerController extends Controller
{
    private MedewerkerModel $medewerkerModel;

    public function __construct()
    {
        parent::__construct();
        $this->medewerkerModel = new MedewerkerModel();
    }

    /**
     * GET /medewerkers
     * Toont overzicht van medewerkers met optioneel specialisatie filter
     */
    public function index(): void
    {
        $this->vereisLogin();
        $flash = $this->getFlash();

        // Specialisatie filter
        $gekozenSpecialisatie = trim($_GET['specialisatie'] ?? '');
        $filterActief         = $gekozenSpecialisatie !== '' && $gekozenSpecialisatie !== 'Alle specialisaties';
        $specialisatie        = $filterActief ? $gekozenSpecialisatie : null;

        // Vaste lijst specialisaties voor filter (inclusief Permanent, ook zonder medewerkers)
        $specialisaties = $this->medewerkerModel->getAlleSpecialisaties();

        // Paginering – 4 medewerkers per pagina (volgens wireframe)
        $perPagina     = 4;
        $huidigePagina = max(1, (int)($_GET['pagina'] ?? 1));

        $totaalMedewerkers = $this->medewerkerModel->telMedewerkers($specialisatie);
        $totaalPaginas     = max(1, (int)ceil($totaalMedewerkers / $perPagina));
        $huidigePagina     = min($huidigePagina, $totaalPaginas);
        $offset            = ($huidigePagina - 1) * $perPagina;

        $medewerkers = $this->medewerkerModel->haalMedewerkersOp($specialisatie, $perPagina, $offset);

        // Log de actie
        $this->medewerkerModel->logTechnischeActie(
            'INFO',
            'MedewerkerController',
            'Medewerkers overzicht bekeken',
            json_encode([
                'specialisatie' => $specialisatie ?? 'alle',
                'pagina'        => $huidigePagina,
                'totaal'        => $totaalMedewerkers
            ])
        );

        // Indien geen medewerkers gevonden met de geselecteerde specialisatie
        $geenResultaten = $filterActief && $totaalMedewerkers === 0;

        $this->view('medewerkers/index', compact(
            'flash',
            'medewerkers',
            'huidigePagina',
            'totaalPaginas',
            'totaalMedewerkers',
            'gekozenSpecialisatie',
            'filterActief',
            'specialisaties',
            'geenResultaten'
        ));
    }

    /**
     * GET /medewerkers/detail
     * Toont detailpagina van één medewerker
     */
    public function detail(): void
    {
        $this->vereisLogin();
        $id         = (int) ($_GET['id'] ?? 0);
        $flash      = $this->getFlash();
        $medewerker = $this->medewerkerModel->vindOpId($id);

        if (!$medewerker) {
            $this->setFlash('error', 'Medewerker niet gevonden.');
            $this->redirect('/medewerkers');
        }

        $this->medewerkerModel->logTechnischeActie(
            'INFO',
            'MedewerkerController',
            'Medewerker detail bekeken',
            json_encode(['medewerker_id' => $id])
        );

        $this->view('medewerkers/detail', compact('medewerker', 'flash'));
    }

    /**
     * GET /medewerkers/wijzigen
     * Toont het wijzigformulier voor één medewerker
     */
    public function wijzigenForm(): void
    {
        $this->vereisLogin();
        $id         = (int) ($_GET['id'] ?? 0);
        $medewerker = $this->medewerkerModel->vindOpId($id);

        if (!$medewerker) {
            $this->setFlash('error', 'Medewerker niet gevonden.');
            $this->redirect('/medewerkers');
        }

        $csrfToken      = $this->genereerCsrfToken();
        $flash          = $this->getFlash();
        $specialisaties = $this->medewerkerModel->getAlleSpecialisaties();

        $this->view('medewerkers/wijzigen', compact('csrfToken', 'flash', 'medewerker', 'specialisaties'));
    }

    /**
     * POST /medewerkers/wijzigen
     * Verwerkt het wijzigformulier
     */
    public function wijzigen(): void
    {
        $this->vereisLogin();

        // CSRF
        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldig CSRF-token.');
            $this->redirect('/medewerkers');
        }

        $id            = (int)  ($_POST['id']            ?? 0);
        $specialisatie = trim(   $_POST['specialisatie']  ?? '');
        $geboortedatum = trim(   $_POST['geboortedatum']  ?? '');
        $contactEmail  = trim(   $_POST['contact_email']  ?? '');
        $straatnaam    = trim(   $_POST['straatnaam']     ?? '');
        $huisnummer    = trim(   $_POST['huisnummer']     ?? '');
        $toevoeging    = trim(   $_POST['toevoeging']     ?? '');
        $postcode      = trim(   $_POST['postcode']       ?? '');
        $plaats        = trim(   $_POST['plaats']         ?? '');
        $mobiel        = trim(   $_POST['mobiel']         ?? '');
        $opmerking     = trim(   $_POST['opmerking']      ?? '');

        // Medewerker ophalen
        $medewerker = $this->medewerkerModel->vindOpId($id);
        if (!$medewerker) {
            $this->setFlash('error', 'Medewerker niet gevonden.');
            $this->redirect('/medewerkers');
        }

        // ── Serverside validatie ──────────────────────────────────────
        $validatieErrors = [];

        if ($f = Validator::foutVerplicht($specialisatie, 'Specialisatie')) {
            $validatieErrors['specialisatie'] = $f;
        }
        if (empty($geboortedatum)) {
            $validatieErrors['geboortedatum'] = 'Geboortedatum is verplicht';
        } elseif (!strtotime($geboortedatum)) {
            $validatieErrors['geboortedatum'] = 'Voer een geldige datum in';
        }
        if ($f = Validator::foutEmail($contactEmail, 'Contact e-mail')) {
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

        // Minderjarige + Permanent check (ook client-side gevangen, maar ook hier)
        if (empty($validatieErrors['geboortedatum']) && !empty($geboortedatum)) {
            $leeftijd = $this->medewerkerModel->berekenLeeftijd($geboortedatum);
            if ($leeftijd < 18 && $specialisatie === 'Permanent') {
                $validatieErrors['specialisatie'] =
                    'Minderjarige medewerkers mogen geen specialisatie Permanent toegewezen krijgen '
                    . 'vanwege het werken met gevaarlijke stoffen en chemicaliën.';
            }
        }

        $specialisaties = $this->medewerkerModel->getAlleSpecialisaties();

        // Bij validatiefouten: direct terug naar formulier
        if (!empty($validatieErrors)) {
            $this->medewerkerModel->logTechnischeActie(
                'WARNING', 'MedewerkerController',
                'Wijzigen medewerker mislukt (validatie)',
                json_encode(['id' => $id, 'errors' => $validatieErrors])
            );

            $csrfToken = $this->genereerCsrfToken();
            $flash     = [
                'type'    => 'error',
                'bericht' => 'Medewerkergegevens zijn niet bijgewerkt',
                'errors'  => $validatieErrors,
            ];

            // Overschrijf medewerkerdata met ingevoerde waarden
            $medewerker['Specialisatie'] = $specialisatie;
            $medewerker['Geboortedatum'] = $geboortedatum;
            $medewerker['ContactEmail']  = $contactEmail;
            $medewerker['Straatnaam']    = $straatnaam;
            $medewerker['Huisnummer']    = $huisnummer;
            $medewerker['Toevoeging']    = $toevoeging;
            $medewerker['Postcode']      = $postcode;
            $medewerker['Plaats']        = $plaats;
            $medewerker['Mobiel']        = $mobiel;
            $medewerker['Opmerking']     = $opmerking;

            $this->view('medewerkers/wijzigen', compact('csrfToken', 'flash', 'medewerker', 'specialisaties'));
            return;
        }

        // ── Opslaan via stored procedure ─────────────────────────────
        $resultaat = $this->medewerkerModel->wijzigMedewerker($id, [
            'specialisatie' => $specialisatie,
            'geboortedatum' => $geboortedatum,
            'contact_email' => $contactEmail,
            'straatnaam'    => $straatnaam,
            'huisnummer'    => $huisnummer,
            'toevoeging'    => $toevoeging,
            'postcode'      => $postcode,
            'plaats'        => $plaats,
            'mobiel'        => $mobiel,
            'opmerking'     => $opmerking,
        ]);

        if (!$resultaat['success']) {
            // Stored procedure gaf fout terug (bijv. nog een extra leeftijdscheck)
            $this->medewerkerModel->logTechnischeActie(
                'WARNING', 'MedewerkerController',
                'Wijzigen medewerker geweigerd door stored procedure',
                json_encode(['id' => $id, 'message' => $resultaat['message']])
            );

            $csrfToken = $this->genereerCsrfToken();
            $flash     = [
                'type'    => 'error',
                'bericht' => 'Medewerkergegevens zijn niet bijgewerkt',
                'errors'  => ['specialisatie' => $resultaat['message']],
            ];

            $medewerker['Specialisatie'] = $specialisatie;
            $medewerker['Geboortedatum'] = $geboortedatum;
            $medewerker['ContactEmail']  = $contactEmail;
            $medewerker['Straatnaam']    = $straatnaam;
            $medewerker['Huisnummer']    = $huisnummer;
            $medewerker['Toevoeging']    = $toevoeging;
            $medewerker['Postcode']      = $postcode;
            $medewerker['Plaats']        = $plaats;
            $medewerker['Mobiel']        = $mobiel;
            $medewerker['Opmerking']     = $opmerking;

            $this->view('medewerkers/wijzigen', compact('csrfToken', 'flash', 'medewerker', 'specialisaties'));
            return;
        }

        // ── Succes ───────────────────────────────────────────────────
        $this->medewerkerModel->logTechnischeActie(
            'INFO', 'MedewerkerController',
            'Medewerkergegevens bijgewerkt',
            json_encode(['id' => $id, 'specialisatie' => $specialisatie])
        );

        $this->setFlash('success', 'Medewerkergegevens bijgewerkt');
        $this->redirect('/medewerkers/detail?id=' . $id);
    }
}
