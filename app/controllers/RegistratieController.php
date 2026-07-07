<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Klant;
use App\Models\Allergeen;

/**
 * Afhandeling van publieke klantregistratie.
 */
class RegistratieController extends Controller
{
    private Klant     $klantModel;
    private Allergeen $allegeenModel;

    public function __construct()
    {
        parent::__construct();
        $this->klantModel    = new Klant();
        $this->allegeenModel = new Allergeen();
    }

    /** Toon het registratieformulier. */
    public function registrerenForm(): void
    {
        // Al ingelogd? Stuur door.
        if (!empty($_SESSION['gebruiker_id'])) {
            $this->redirect('/dashboard');
        }

        $csrfToken      = $this->genereerCsrfToken();
        $flash          = $this->getFlash();
        $alleAllergenen = $this->allegeenModel->alle();
        $geselecteerd   = $_SESSION['form_data']['allergenen'] ?? [];
        $oud            = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);

        $this->view(
            'auth/register',
            compact('csrfToken', 'flash', 'alleAllergenen', 'geselecteerd', 'oud'),
            'layouts/public'
        );
    }

    /** Verwerk het registratieformulier. */
    public function registreren(): void
    {
        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldig verzoek (CSRF).');
            $this->redirect('/registreren');
        }

        $data = [
            'naam'           => trim($_POST['naam'] ?? ''),
            'email'          => strtolower(trim($_POST['email'] ?? '')),
            'wachtwoord'     => $_POST['wachtwoord'] ?? '',
            'adres'          => trim($_POST['adres'] ?? ''),
            'telefoonnummer' => trim($_POST['telefoonnummer'] ?? ''),
            'wensen'         => trim($_POST['wensen'] ?? ''),
            'allergenen'     => array_map('intval', $_POST['allergenen'] ?? []),
        ];

        $fouten = $this->valideer($data);

        if (!empty($fouten)) {
            $this->setFlash('error', implode('<br>', $fouten));
            $_SESSION['form_data'] = $data;
            $this->redirect('/registreren');
        }

        $resultaat = $this->klantModel->aanmaken($data);

        if ($resultaat['fout'] !== '') {
            if (str_contains($resultaat['fout'], 'E-mailadres')) {
                $this->setFlash(
                    'error',
                    'Het e-mailadres <strong>' . htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8')
                    . '</strong> is al in gebruik. Probeer in te loggen of gebruik een ander adres.'
                );
            } else {
                $this->setFlash('error', $resultaat['fout']);
            }
            $_SESSION['form_data'] = $data;
            $this->redirect('/registreren');
        }

        $this->setFlash('success', 'Account succesvol aangemaakt! Je kunt nu inloggen.');
        $this->redirect('/login');
    }

    /**
     * Valideer registratieformulier.
     *
     * @return string[]
     */
    private function valideer(array $data): array
    {
        $fouten = [];

        if ($data['naam'] === '') {
            $fouten[] = '<strong>Naam</strong> is verplicht.';
        } elseif (strlen($data['naam']) < 2 || strlen($data['naam']) > 100) {
            $fouten[] = '<strong>Naam</strong> moet tussen 2 en 100 tekens zijn.';
        }

        if ($data['email'] === '') {
            $fouten[] = '<strong>E-mailadres</strong> is verplicht.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $fouten[] = '<strong>E-mailadres</strong> heeft een ongeldig formaat.';
        }

        if ($data['wachtwoord'] === '') {
            $fouten[] = '<strong>Wachtwoord</strong> is verplicht.';
        } else {
            if (strlen($data['wachtwoord']) < 8) {
                $fouten[] = '<strong>Wachtwoord</strong> moet minimaal 8 tekens bevatten.';
            }
            if (!preg_match('/[A-Z]/', $data['wachtwoord'])) {
                $fouten[] = '<strong>Wachtwoord</strong> moet minimaal één hoofdletter bevatten.';
            }
            if (!preg_match('/[a-z]/', $data['wachtwoord'])) {
                $fouten[] = '<strong>Wachtwoord</strong> moet minimaal één kleine letter bevatten.';
            }
            if (!preg_match('/[0-9]/', $data['wachtwoord'])) {
                $fouten[] = '<strong>Wachtwoord</strong> moet minimaal één cijfer bevatten.';
            }
        }

        if ($data['telefoonnummer'] !== '') {
            $tel = preg_replace('/[\s\-\.\(\)]/', '', $data['telefoonnummer']);
            if (!preg_match('/^06[0-9]{8}$/', $tel)
                && !preg_match('/^0[1-9][0-9]{7,8}$/', $tel)
                && !preg_match('/^\+[1-9][0-9]{6,13}$/', $tel)
            ) {
                $fouten[] = '<strong>Telefoonnummer</strong> is ongeldig '
                    . '(bijv. <code>0612345678</code>, <code>020-1234567</code> of <code>+31612345678</code>).';
            }
        }

        return $fouten;
    }
}
