<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\KlantModel;

/**
 * CRUD-beheer voor klanten (alleen medewerker/eigenaar).
 */
class KlantController extends Controller
{
    private KlantModel $klantModel;

    public function __construct()
    {
        parent::__construct();
        $this->klantModel = new KlantModel();
    }

    // ----------------------------------------------------------------
    // GET /klanten
    // ----------------------------------------------------------------
    public function index(): void
    {
        $this->vereisLogin();
        $flash   = $this->getFlash();
        $klanten = $this->klantModel->alleKlanten();

        $this->view('klanten/index', compact('flash', 'klanten'));
    }

    // ----------------------------------------------------------------
    // GET /klanten/detail
    // ----------------------------------------------------------------
    public function detail(): void
    {
        $this->vereisLogin();
        $id    = (int) ($_GET['id'] ?? 0);
        $klant = $this->klantModel->vindOpId($id);

        if (!$klant) {
            $this->setFlash('error', 'Klant niet gevonden.');
            $this->redirect('/klanten');
        }

        $this->view('klanten/detail', compact('klant'));
    }

    // ----------------------------------------------------------------
    // GET /klanten/aanmaken
    // ----------------------------------------------------------------
    public function aanmakenForm(): void
    {
        $this->vereisLogin();
        $csrfToken = $this->genereerCsrfToken();
        $flash     = $this->getFlash();
        $oud       = [];

        $this->view('klanten/aanmaken', compact('csrfToken', 'flash', 'oud'));
    }

    // ----------------------------------------------------------------
    // POST /klanten/aanmaken  (stub – uitbreidbaar)
    // ----------------------------------------------------------------
    public function aanmaken(): void
    {
        $this->vereisLogin();
        $this->setFlash('success', 'Klant aangemaakt.');
        $this->redirect('/klanten');
    }

    // ----------------------------------------------------------------
    // GET /klanten/wijzigen
    // ----------------------------------------------------------------
    public function wijzigenForm(): void
    {
        $this->vereisLogin();
        $id    = (int) ($_GET['id'] ?? 0);
        $klant = $this->klantModel->vindOpId($id);

        if (!$klant) {
            $this->setFlash('error', 'Klant niet gevonden.');
            $this->redirect('/klanten');
        }

        $csrfToken = $this->genereerCsrfToken();
        $flash     = $this->getFlash();

        $this->view('klanten/wijzigen', compact('csrfToken', 'flash', 'klant'));
    }

    // ----------------------------------------------------------------
    // POST /klanten/wijzigen  (stub)
    // ----------------------------------------------------------------
    public function wijzigen(): void
    {
        $this->vereisLogin();
        $this->setFlash('success', 'Klantgegevens bijgewerkt.');
        $this->redirect('/klanten');
    }

    // ----------------------------------------------------------------
    // POST /klanten/verwijderen  (stub)
    // ----------------------------------------------------------------
    public function verwijderen(): void
    {
        $this->vereisLogin();
        $this->setFlash('success', 'Klant verwijderd.');
        $this->redirect('/klanten');
    }
}
