<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\KlantModel;

/**
 * Dashboard voor ingelogde medewerkers/eigenaar.
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        $this->vereisLogin();

        $gebruikerNaam = $_SESSION['gebruiker_naam'] ?? 'Gebruiker';
        $gebruikerRol  = $_SESSION['gebruiker_rol']  ?? 'klant';
        $flash         = $this->getFlash();

        // Haal statistieken op – veilig afvangen als tabel nog niet bestaat
        $aantalKlanten    = 0;
        $aantalMedewerkers = 0;
        try {
            $klantModel        = new KlantModel();
            $aantalKlanten     = count($klantModel->alleKlanten());
            $aantalMedewerkers = $klantModel->aantalMedewerkers();
        } catch (\Throwable $e) {
            $this->logger->warning('Dashboard stats ophalen mislukt: ' . $e->getMessage());
        }

        $this->view('dashboard/index', compact(
            'gebruikerNaam', 'gebruikerRol', 'flash', 'aantalKlanten', 'aantalMedewerkers'
        ));
    }
}
