<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\Klant;
use App\Models\Allergeen;

/**
 * Beheert CRUD-acties voor klanten.
 */
class KlantController extends Controller
{
    private Klant    $klantModel;
    private Allergeen $allegeenModel;

    public function __construct()
    {
        parent::__construct();
        $this->klantModel    = new Klant();
        $this->allegeenModel = new Allergeen();
        
        // Overschrijf logger om klanten.log te gebruiken
        $this->logger = new Logger(dirname(__DIR__, 2) . '/logs/klanten.log');
    }

    // -------------------------------------------------------
    // Overzicht
    // -------------------------------------------------------

    public function index(): void
    {
        $this->vereisLogin();
        $klanten   = $this->klantModel->overzicht();
        $flash     = $this->getFlash();
        $csrfToken = $this->genereerCsrfToken();
        $this->view('klanten/index', compact('klanten', 'flash', 'csrfToken'));
    }

    // -------------------------------------------------------
    // Detail
    // -------------------------------------------------------

    public function detail(): void
    {
        $this->vereisLogin();

        $id    = (int) ($_GET['id'] ?? 0);
        $klant = $this->klantModel->vindOpId($id);

        if ($klant === null) {
            $this->setFlash('error', 'Klant niet gevonden.');
            $this->redirect('/klanten');
        }

        $allergenen = $this->allegeenModel->namenVanKlant($id);
        $flash      = $this->getFlash();
        $csrfToken  = $this->genereerCsrfToken();
        $this->view('klanten/detail', compact('klant', 'allergenen', 'flash', 'csrfToken'));
    }

    // -------------------------------------------------------
    // Aanmaken
    // -------------------------------------------------------

    public function aanmakenForm(): void
    {
        $this->vereisLogin();
        $csrfToken       = $this->genereerCsrfToken();
        $flash           = $this->getFlash();
        $oud             = $_SESSION['form_data'] ?? [];
        $alleAllergenen  = $this->allegeenModel->alle();
        $geselecteerd    = $oud['allergenen'] ?? [];
        unset($_SESSION['form_data']);
        $this->view('klanten/create', compact('csrfToken', 'flash', 'oud', 'alleAllergenen', 'geselecteerd'));
    }

    public function aanmaken(): void
    {
        $this->vereisLogin();

        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldig verzoek (CSRF).');
            $this->redirect('/klanten/aanmaken');
        }

        $data   = $this->haalFormDataOp();
        $fouten = $this->valideerKlantData($data, true);

        if (!empty($fouten)) {
            $this->setFlash('error', implode('<br>', $fouten));
            $_SESSION['form_data'] = $data;
            $this->redirect('/klanten/aanmaken');
        }

        $resultaat = $this->klantModel->aanmaken($data);

        if ($resultaat['fout'] !== '') {
            // Geef specifieke foutmelding bij dubbel e-mailadres
            if (str_contains($resultaat['fout'], 'E-mailadres')) {
                $rol = $_SESSION['rol'] ?? 'medewerker';
                $melding = ($rol === 'eigenaar' || $rol === 'medewerker')
                    ? 'Het e-mailadres <strong>' . htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8')
                      . '</strong> is al in gebruik door een andere gebruiker in het systeem.'
                    : $resultaat['fout'];
                $this->setFlash('error', $melding);
            } else {
                $this->setFlash('error', $resultaat['fout']);
            }
            $_SESSION['form_data'] = $data;
            $this->redirect('/klanten/aanmaken');
        }

        $this->setFlash('success', 'Klant succesvol aangemaakt.');
        $this->redirect('/klanten');
    }

    // -------------------------------------------------------
    // Wijzigen
    // -------------------------------------------------------

    public function wijzigenForm(): void
    {
        $this->vereisLogin();

        $id    = (int) ($_GET['id'] ?? 0);
        $klant = $this->klantModel->vindOpId($id);

        if ($klant === null) {
            $this->setFlash('error', 'Klant niet gevonden.');
            $this->redirect('/klanten');
        }

        $csrfToken      = $this->genereerCsrfToken();
        $flash          = $this->getFlash();
        $oud            = $_SESSION['form_data'] ?? [];
        $alleAllergenen = $this->allegeenModel->alle();
        unset($_SESSION['form_data']);

        // Geselecteerde allergenen: uit sessie bij validatiefout, anders uit DB
        $geselecteerd = !empty($oud)
            ? ($oud['allergenen'] ?? [])
            : $this->allegeenModel->vanKlant($id);

        $formData = !empty($oud) ? $oud : $klant;

        $this->view('klanten/edit', compact(
            'csrfToken', 'flash', 'klant', 'formData',
            'alleAllergenen', 'geselecteerd'
        ));
    }

    public function wijzigen(): void
    {
        $this->vereisLogin();

        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldig verzoek (CSRF).');
            $this->redirect('/klanten');
        }

        $id     = (int) ($_POST['id'] ?? 0);
        $data   = $this->haalFormDataOp();
        $fouten = $this->valideerKlantData($data, false);

        if (!empty($fouten)) {
            $this->setFlash('error', implode('<br>', $fouten));
            $_SESSION['form_data'] = $data;
            $this->redirect("/klanten/wijzigen?id={$id}");
        }

        $fout = $this->klantModel->wijzigen($id, $data);

        if ($fout !== '') {
            if (str_contains($fout, 'E-mailadres')) {
                $melding = 'Het e-mailadres <strong>' . htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8')
                    . '</strong> is al in gebruik door een andere gebruiker in het systeem.';
                $this->setFlash('error', $melding);
            } else {
                $this->setFlash('error', $fout);
            }
            $_SESSION['form_data'] = $data;
            $this->redirect("/klanten/wijzigen?id={$id}");
        }

        $this->setFlash('success', 'Klant succesvol bijgewerkt.');
        $this->redirect('/klanten');
    }

    // -------------------------------------------------------
    // Verwijderen
    // -------------------------------------------------------

    public function verwijderen(): void
    {
        $this->vereisLogin();

        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->logger->warning('CSRF gefaald bij verwijderen klant id=' . ($_POST['id'] ?? '?'));
            $this->setFlash('error', 'Sessie verlopen. Probeer de pagina te herladen.');
            $this->redirect('/klanten');
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->setFlash('error', 'Ongeldig klant-ID.');
            $this->redirect('/klanten');
        }

        $fout = $this->klantModel->verwijderen($id);

        if ($fout !== '') {
            $this->logger->error("Verwijderen klant id={$id} mislukt: {$fout}");
            $this->setFlash('error', 'Verwijderen mislukt: ' . $fout);
        } else {
            $this->logger->info("Klant id={$id} succesvol verwijderd.");
            $this->setFlash('success', 'Klant succesvol verwijderd.');
        }

        $this->redirect('/klanten');
    }

    // -------------------------------------------------------
    // Hulpmethoden
    // -------------------------------------------------------

    /**
     * Lees en saniteer alle formuliervelden uit $_POST.
     *
     * @return array<string,mixed>
     */
    private function haalFormDataOp(): array
    {
        return [
            'naam'           => trim($_POST['naam'] ?? ''),
            'email'          => strtolower(trim($_POST['email'] ?? '')),
            'wachtwoord'     => $_POST['wachtwoord'] ?? '',
            'adres'          => trim($_POST['adres'] ?? ''),
            'telefoonnummer' => trim($_POST['telefoonnummer'] ?? ''),
            'wensen'         => trim($_POST['wensen'] ?? ''),
            // Allergenen komen als array van integer-IDs
            'allergenen'     => array_map('intval', $_POST['allergenen'] ?? []),
        ];
    }

    /**
     * Gedetailleerde server-side validatie.
     *
     * @param  array<string,mixed> $data
     * @param  bool                $isNieuw
     * @return string[]
     */
    private function valideerKlantData(array $data, bool $isNieuw): array
    {
        $fouten = [];

        // --- Naam ---
        if ($data['naam'] === '') {
            $fouten[] = '<strong>Naam</strong> is verplicht.';
        } elseif (strlen($data['naam']) < 2) {
            $fouten[] = '<strong>Naam</strong> moet minimaal 2 tekens bevatten.';
        } elseif (strlen($data['naam']) > 100) {
            $fouten[] = '<strong>Naam</strong> mag maximaal 100 tekens bevatten.';
        } elseif (!preg_match('/^[\pL\s\'\-\.]+$/u', $data['naam'])) {
            $fouten[] = '<strong>Naam</strong> mag alleen letters, spaties, koppeltekens en punten bevatten.';
        }

        // --- E-mail ---
        if ($data['email'] === '') {
            $fouten[] = '<strong>E-mailadres</strong> is verplicht.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $fouten[] = '<strong>E-mailadres</strong> heeft een ongeldig formaat (bijv. naam@domein.nl).';
        } elseif (strlen($data['email']) > 255) {
            $fouten[] = '<strong>E-mailadres</strong> mag maximaal 255 tekens bevatten.';
        }

        // --- Wachtwoord ---
        if ($isNieuw) {
            if ($data['wachtwoord'] === '') {
                $fouten[] = '<strong>Wachtwoord</strong> is verplicht bij aanmaken.';
            } else {
                $fouten = array_merge($fouten, $this->valideerWachtwoord($data['wachtwoord']));
            }
        } elseif ($data['wachtwoord'] !== '') {
            $fouten = array_merge($fouten, $this->valideerWachtwoord($data['wachtwoord']));
        }

        // --- Telefoonnummer ---
        if ($data['telefoonnummer'] !== '') {
            // Normaliseer: verwijder spaties, koppeltekens, haakjes
            $telClean = preg_replace('/[\s\-\.\(\)]/', '', $data['telefoonnummer']);

            $geldig = false;

            // NL mobiel: 06 + 8 cijfers (bijv. 0612345678)
            if (preg_match('/^06[0-9]{8}$/', $telClean)) {
                $geldig = true;
            }
            // NL vast: 0[1-9] + 7-8 cijfers (bijv. 0201234567, 0301234567)
            elseif (preg_match('/^0[1-9][0-9]{7,8}$/', $telClean)) {
                $geldig = true;
            }
            // Internationaal: + gevolgd door 7-14 cijfers (bijv. +31612345678)
            elseif (preg_match('/^\+[1-9][0-9]{6,13}$/', $telClean)) {
                $geldig = true;
            }

            if (!$geldig) {
                $fouten[] = '<strong>Telefoonnummer</strong> is ongeldig. '
                    . 'Gebruik bijv. <code>0612345678</code>, <code>020-1234567</code> of <code>+31612345678</code>.';
            }
        }

        // --- Adres ---
        if ($data['adres'] !== '' && strlen($data['adres']) > 255) {
            $fouten[] = '<strong>Adres</strong> mag maximaal 255 tekens bevatten.';
        }

        // --- Wensen ---
        if ($data['wensen'] !== '' && strlen($data['wensen']) > 1000) {
            $fouten[] = '<strong>Wensen</strong> mogen maximaal 1000 tekens bevatten.';
        }

        return $fouten;
    }

    /**
     * Valideer wachtwoordsterkte.
     *
     * @return string[]
     */
    private function valideerWachtwoord(string $ww): array
    {
        $fouten = [];
        if (strlen($ww) < 8) {
            $fouten[] = '<strong>Wachtwoord</strong> moet minimaal 8 tekens bevatten.';
        }
        if (strlen($ww) > 72) {
            $fouten[] = '<strong>Wachtwoord</strong> mag maximaal 72 tekens bevatten.';
        }
        if (!preg_match('/[A-Z]/', $ww)) {
            $fouten[] = '<strong>Wachtwoord</strong> moet minimaal één hoofdletter bevatten.';
        }
        if (!preg_match('/[a-z]/', $ww)) {
            $fouten[] = '<strong>Wachtwoord</strong> moet minimaal één kleine letter bevatten.';
        }
        if (!preg_match('/[0-9]/', $ww)) {
            $fouten[] = '<strong>Wachtwoord</strong> moet minimaal één cijfer bevatten.';
        }
        return $fouten;
    }
}
