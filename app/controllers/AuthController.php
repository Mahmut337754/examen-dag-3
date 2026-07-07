<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

/**
 * Verwerkt inloggen, uitloggen en wachtwoord wijzigen.
 */
class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    // -------------------------------------------------------
    // Inloggen
    // -------------------------------------------------------

    /** Toon het inlogformulier. */
    public function loginForm(): void
    {
        // Stuur ingelogde gebruiker direct door naar dashboard
        if (!empty($_SESSION['gebruiker_id'])) {
            $this->redirect('/dashboard');
        }

        $csrfToken = $this->genereerCsrfToken();
        $flash     = $this->getFlash();
        $this->view('auth/login', compact('csrfToken', 'flash'), 'layouts/public');
    }

    /** Verwerk het ingediende inlogformulier. */
    public function login(): void
    {
        // CSRF-controle
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->valideerCsrfToken($token)) {
            $this->setFlash('error', 'Ongeldig verzoek. Probeer opnieuw.');
            $this->redirect('/login');
        }

        $email      = trim($_POST['email'] ?? '');
        $wachtwoord = $_POST['wachtwoord'] ?? '';

        // Server-side validatie
        $fouten = [];
        if ($email === '') {
            $fouten[] = 'E-mailadres is verplicht.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fouten[] = 'Ongeldig e-mailformaat.';
        }
        if ($wachtwoord === '') {
            $fouten[] = 'Wachtwoord is verplicht.';
        }

        if (!empty($fouten)) {
            $this->setFlash('error', implode(' ', $fouten));
            $this->redirect('/login');
        }

        // Gebruiker opzoeken
        $gebruiker = $this->userModel->vindOpEmail($email);

        if ($gebruiker === null
            || !$gebruiker['is_actief']
            || !password_verify($wachtwoord, $gebruiker['wachtwoord'])
        ) {
            $this->logger->warning("Mislukte inlogpoging voor email: {$email}");
            $this->setFlash('error', 'Ongeldige inloggegevens.');
            $this->redirect('/login');
        }

        // Vernieuw sessie-ID ter voorkoming van session fixation
        session_regenerate_id(true);

        $_SESSION['gebruiker_id']  = (int) $gebruiker['id'];
        $_SESSION['gebruiker_naam'] = $gebruiker['naam'];
        $_SESSION['rol']           = $gebruiker['rol_naam'];

        $this->logger->info("Ingelogd: gebruiker id={$gebruiker['id']}, rol={$gebruiker['rol_naam']}");
        $this->redirect('/dashboard');
    }

    // -------------------------------------------------------
    // Uitloggen
    // -------------------------------------------------------

    /** Vernietig de sessie en stuur door naar login. */
    public function logout(): void
    {
        $id = $_SESSION['gebruiker_id'] ?? 'onbekend';
        session_unset();
        session_destroy();
        $this->logger->info("Gebruiker id={$id} uitgelogd.");
        $this->redirect('/login');
    }

    // -------------------------------------------------------
    // Wachtwoord wijzigen
    // -------------------------------------------------------

    /** Toon het formulier voor wachtwoord wijzigen. */
    public function wachtwoordWijzigenForm(): void
    {
        $this->vereisLogin();
        $csrfToken = $this->genereerCsrfToken();
        $flash     = $this->getFlash();
        $this->view('auth/change-password', compact('csrfToken', 'flash'));
    }

    /** Verwerk het ingediende formulier voor wachtwoord wijzigen. */
    public function wachtwoordWijzigen(): void
    {
        $this->vereisLogin();

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->valideerCsrfToken($token)) {
            $this->setFlash('error', 'Ongeldig verzoek.');
            $this->redirect('/wachtwoord-wijzigen');
        }

        $huidig   = $_POST['huidig_wachtwoord'] ?? '';
        $nieuw    = $_POST['nieuw_wachtwoord'] ?? '';
        $bevestig = $_POST['bevestig_wachtwoord'] ?? '';

        // Valideer invoer
        $fouten = [];
        if ($huidig === '') {
            $fouten[] = 'Huidig wachtwoord is verplicht.';
        }
        if (strlen($nieuw) < 8) {
            $fouten[] = 'Nieuw wachtwoord moet minimaal 8 tekens bevatten.';
        }
        if ($nieuw !== $bevestig) {
            $fouten[] = 'Nieuwe wachtwoorden komen niet overeen.';
        }

        if (!empty($fouten)) {
            $this->setFlash('error', implode(' ', $fouten));
            $this->redirect('/wachtwoord-wijzigen');
        }

        // Controleer huidig wachtwoord
        $gebruikerId = (int) $_SESSION['gebruiker_id'];
        $gebruiker   = $this->userModel->vindOpId($gebruikerId);

        if ($gebruiker === null || !password_verify($huidig, $gebruiker['wachtwoord'])) {
            $this->setFlash('error', 'Huidig wachtwoord is onjuist.');
            $this->redirect('/wachtwoord-wijzigen');
        }

        // Sla nieuw wachtwoord op
        $gelukt = $this->userModel->wijzigWachtwoord($gebruikerId, $nieuw);

        if ($gelukt) {
            $this->setFlash('success', 'Wachtwoord succesvol gewijzigd.');
        } else {
            $this->setFlash('error', 'Fout bij opslaan nieuw wachtwoord.');
        }

        $this->redirect('/wachtwoord-wijzigen');
    }
}
