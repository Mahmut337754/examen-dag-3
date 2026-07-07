<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\KlantModel;

/**
 * Verwerkt publieke klantregistratie.
 */
class RegistratieController extends Controller
{
    private UserModel  $userModel;
    private KlantModel $klantModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel  = new UserModel();
        $this->klantModel = new KlantModel();
    }

    // ----------------------------------------------------------------
    // GET /registreren
    // ----------------------------------------------------------------
    public function registrerenForm(): void
    {
        if (!empty($_SESSION['gebruiker_id'])) {
            $this->redirect('/dashboard');
        }

        $csrfToken      = $this->genereerCsrfToken();
        $flash          = $this->getFlash();
        $oud            = $_SESSION['reg_oud'] ?? [];
        $alleAllergenen = [];   // geen allergenen-tabel aanwezig; lege array
        $geselecteerd   = [];

        unset($_SESSION['reg_oud']);

        $this->view('auth/registreren', compact(
            'csrfToken', 'flash', 'oud', 'alleAllergenen', 'geselecteerd'
        ));
    }

    // ----------------------------------------------------------------
    // POST /registreren
    // ----------------------------------------------------------------
    public function registreren(): void
    {
        // CSRF
        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldige sessie. Probeer opnieuw.');
            $this->redirect('/registreren');
        }

        $naam       = trim($_POST['naam']       ?? '');
        $email      = trim($_POST['email']      ?? '');
        $wachtwoord = $_POST['wachtwoord']      ?? '';
        $bevestig   = $_POST['wachtwoord_bevestig'] ?? '';

        // --- Validatie ---
        $fouten = [];

        if (strlen($naam) < 2) {
            $fouten[] = 'Naam is verplicht (min. 2 tekens).';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fouten[] = 'Voer een geldig e-mailadres in.';
        }

        if (strlen($wachtwoord) < 8) {
            $fouten[] = 'Wachtwoord moet minimaal 8 tekens bevatten.';
        }

        if ($wachtwoord !== $bevestig) {
            $fouten[] = 'Wachtwoorden komen niet overeen.';
        }

        if ($this->userModel->emailBestaat($email)) {
            $fouten[] = 'Dit e-mailadres is al in gebruik.';
        }

        if (!empty($fouten)) {
            $_SESSION['reg_oud'] = ['naam' => $naam, 'email' => $email];
            $this->setFlash('error', implode(' ', $fouten));
            $this->redirect('/registreren');
        }

        // --- Opslaan ---
        $hash   = password_hash($wachtwoord, PASSWORD_BCRYPT);
        $userId = $this->userModel->maakAan($naam, $email, $hash);

        // Maak bijbehorend Klant-record aan
        $this->klantModel->maakAan($userId, $naam);

        $this->logger->info("Nieuw klantaccount aangemaakt: {$email} (userId: {$userId})");

        $this->setFlash('success', 'Account aangemaakt! U kunt nu inloggen.');
        $this->redirect('/login');
    }
}
