<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

/**
 * Verwerkt inloggen, uitloggen en wachtwoord wijzigen.
 */
class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    // ----------------------------------------------------------------
    // GET /login
    // ----------------------------------------------------------------
    public function loginForm(): void
    {
        if (!empty($_SESSION['gebruiker_id'])) {
            $this->redirect('/dashboard');
        }

        $csrfToken = $this->genereerCsrfToken();
        $flash     = $this->getFlash();

        $this->view('auth/login', compact('csrfToken', 'flash'));
    }

    // ----------------------------------------------------------------
    // POST /login
    // ----------------------------------------------------------------
    public function login(): void
    {
        // CSRF
        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldige sessie. Probeer opnieuw.');
            $this->redirect('/login');
        }

        $email      = trim($_POST['email'] ?? '');
        $wachtwoord = $_POST['wachtwoord'] ?? '';

        if ($email === '' || $wachtwoord === '') {
            $this->setFlash('error', 'Vul e-mailadres en wachtwoord in.');
            $this->redirect('/login');
        }

        $gebruiker = $this->userModel->vindOpEmail($email);

        if (!$gebruiker || !password_verify($wachtwoord, $gebruiker['password'])) {
            $this->logger->warning("Mislukte inlogpoging voor e-mail: {$email}");
            $this->setFlash('error', 'Ongeldige inloggegevens.');
            $this->redirect('/login');
        }

        // Sessie regenereren ter voorkoming van session fixation
        session_regenerate_id(true);

        $_SESSION['gebruiker_id']   = $gebruiker['id'];
        $_SESSION['gebruiker_naam'] = $gebruiker['name'];
        $_SESSION['gebruiker_rol']  = $gebruiker['role'];

        $this->logger->info("Gebruiker ingelogd: {$email} (rol: {$gebruiker['role']})");
        $this->redirect('/dashboard');
    }

    // ----------------------------------------------------------------
    // GET /logout
    // ----------------------------------------------------------------
    public function logout(): void
    {
        session_unset();
        session_destroy();
        $this->redirect('/login');
    }

    // ----------------------------------------------------------------
    // GET /wachtwoord-wijzigen
    // ----------------------------------------------------------------
    public function wachtwoordWijzigenForm(): void
    {
        $this->vereisLogin();
        $csrfToken = $this->genereerCsrfToken();
        $flash     = $this->getFlash();

        $this->view('auth/change-password', compact('csrfToken', 'flash'));
    }

    // ----------------------------------------------------------------
    // POST /wachtwoord-wijzigen
    // ----------------------------------------------------------------
    public function wachtwoordWijzigen(): void
    {
        $this->vereisLogin();

        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldige sessie. Probeer opnieuw.');
            $this->redirect('/wachtwoord-wijzigen');
        }

        $huidig  = $_POST['huidig_wachtwoord']  ?? '';
        $nieuw   = $_POST['nieuw_wachtwoord']    ?? '';
        $bevestig= $_POST['bevestig_wachtwoord'] ?? '';

        if ($huidig === '' || $nieuw === '' || $bevestig === '') {
            $this->setFlash('error', 'Alle velden zijn verplicht.');
            $this->redirect('/wachtwoord-wijzigen');
        }

        if (strlen($nieuw) < 8) {
            $this->setFlash('error', 'Nieuw wachtwoord moet minimaal 8 tekens bevatten.');
            $this->redirect('/wachtwoord-wijzigen');
        }

        if ($nieuw !== $bevestig) {
            $this->setFlash('error', 'Wachtwoorden komen niet overeen.');
            $this->redirect('/wachtwoord-wijzigen');
        }

        $gebruikerId = (int) $_SESSION['gebruiker_id'];
        $gebruiker   = $this->userModel->vindOpId($gebruikerId);

        if (!$gebruiker || !password_verify($huidig, $gebruiker['password'])) {
            $this->setFlash('error', 'Huidig wachtwoord is onjuist.');
            $this->redirect('/wachtwoord-wijzigen');
        }

        $hash = password_hash($nieuw, PASSWORD_BCRYPT);
        $this->userModel->werkWachtwoordBij($gebruikerId, $hash);

        $this->logger->info("Wachtwoord gewijzigd voor gebruiker id: {$gebruikerId}");
        $this->setFlash('success', 'Wachtwoord succesvol gewijzigd.');
        $this->redirect('/dashboard');
    }
}
