<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * CRUD-beheer voor producten (placeholder).
 */
class ProductController extends Controller
{
    public function index(): void
    {
        $this->vereisLogin();
        $flash = $this->getFlash();
        $this->view('producten/index', compact('flash'));
    }

    public function detail(): void
    {
        $this->vereisLogin();
        $this->view('producten/detail', []);
    }

    public function aanmakenForm(): void
    {
        $this->vereisLogin();
        $csrfToken = $this->genereerCsrfToken();
        $this->view('producten/aanmaken', compact('csrfToken'));
    }

    public function aanmaken(): void
    {
        $this->vereisLogin();
        $this->setFlash('success', 'Product aangemaakt.');
        $this->redirect('/producten');
    }

    public function wijzigenForm(): void
    {
        $this->vereisLogin();
        $csrfToken = $this->genereerCsrfToken();
        $this->view('producten/wijzigen', compact('csrfToken'));
    }

    public function wijzigen(): void
    {
        $this->vereisLogin();
        $this->setFlash('success', 'Product bijgewerkt.');
        $this->redirect('/producten');
    }

    public function verwijderen(): void
    {
        $this->vereisLogin();
        $this->setFlash('success', 'Product verwijderd.');
        $this->redirect('/producten');
    }
}
