<?php

namespace App\Controllers;

use App\Core\Controller;
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

        // Haal alle unieke specialisaties op voor de dropdown
        $specialisaties = $this->medewerkerModel->getUniekeSpecialisaties();

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

        $this->view('medewerkers/detail', compact('medewerker'));
    }
}
