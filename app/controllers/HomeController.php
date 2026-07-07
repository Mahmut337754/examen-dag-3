<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Publieke homepagina van Kniploket Tiko.
 */
class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /** Toon de homepagina. */
    public function index(): void
    {
        // Ingelogde beheerder sturen we door naar het dashboard
        if (!empty($_SESSION['gebruiker_id'])
            && in_array($_SESSION['rol'] ?? '', ['eigenaar', 'medewerker'])
        ) {
            $this->redirect('/dashboard');
        }

        $this->view('home/index', [], 'layouts/public');
    }
}
