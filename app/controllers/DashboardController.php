<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Klant;

/**
 * Toont het beheerdashboard met basisstatistieken.
 */
class DashboardController extends Controller
{
    private Klant $klantModel;

    public function __construct()
    {
        parent::__construct();
        $this->klantModel = new Klant();
    }

    /** Toon de dashboardpagina. */
    public function index(): void
    {
        $this->vereisLogin();

        $statistieken = $this->klantModel->statistieken();
        $flash        = $this->getFlash();

        $this->view('dashboard/index', compact('statistieken', 'flash'));
    }
}
