<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\Product;
use App\Models\Leverancier;

/**
 * Beheert CRUD-acties voor producten.
 */
class ProductController extends Controller
{
    private Product      $productModel;
    private Leverancier  $leverancierModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel      = new Product();
        $this->leverancierModel  = new Leverancier();
        
        // Overschrijf logger om producten.log te gebruiken
        $this->logger = new Logger(dirname(__DIR__, 2) . '/logs/producten.log');
    }

    // -------------------------------------------------------
    // Overzicht
    // -------------------------------------------------------

    public function index(): void
    {
        $this->vereisLogin();
        $producten  = $this->productModel->overzicht();
        $flash      = $this->getFlash();
        $csrfToken  = $this->genereerCsrfToken();
        $this->view('producten/index', compact('producten', 'flash', 'csrfToken'));
    }

    // -------------------------------------------------------
    // Detail
    // -------------------------------------------------------

    public function detail(): void
    {
        $this->vereisLogin();

        $id      = (int) ($_GET['id'] ?? 0);
        $product = $this->productModel->vindOpId($id);

        if ($product === null) {
            $this->setFlash('error', 'Product niet gevonden.');
            $this->redirect('/producten');
        }

        $flash      = $this->getFlash();
        $csrfToken  = $this->genereerCsrfToken();
        $this->view('producten/detail', compact('product', 'flash', 'csrfToken'));
    }

    // -------------------------------------------------------
    // Aanmaken
    // -------------------------------------------------------

    public function aanmakenForm(): void
    {
        $this->vereisLogin();
        $csrfToken       = $this->genereerCsrfToken();
        $flash           = $this->getFlash();
        $oud             = $_SESSION['form_data'] ?? [];
        $alleLeveranciers = $this->leverancierModel->alle();
        unset($_SESSION['form_data']);
        $this->view('producten/create', compact('csrfToken', 'flash', 'oud', 'alleLeveranciers'));
    }

    public function aanmaken(): void
    {
        $this->vereisLogin();

        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldig verzoek (CSRF).');
            $this->redirect('/producten/aanmaken');
        }

        $data   = $this->haalFormDataOp();
        $fouten = $this->valideerProductData($data);

        if (!empty($fouten)) {
            $this->setFlash('error', implode('<br>', $fouten));
            $_SESSION['form_data'] = $data;
            $this->redirect('/producten/aanmaken');
        }

        $resultaat = $this->productModel->aanmaken($data);

        if ($resultaat['fout'] !== '') {
            $this->setFlash('error', $resultaat['fout']);
            $_SESSION['form_data'] = $data;
            $this->redirect('/producten/aanmaken');
        }

        $this->setFlash('success', 'Product succesvol aangemaakt.');
        $this->redirect('/producten');
    }

    // -------------------------------------------------------
    // Wijzigen
    // -------------------------------------------------------

    public function wijzigenForm(): void
    {
        $this->vereisLogin();

        $id      = (int) ($_GET['id'] ?? 0);
        $product = $this->productModel->vindOpId($id);

        if ($product === null) {
            $this->setFlash('error', 'Product niet gevonden.');
            $this->redirect('/producten');
        }

        $csrfToken       = $this->genereerCsrfToken();
        $flash           = $this->getFlash();
        $oud             = $_SESSION['form_data'] ?? [];
        $alleLeveranciers = $this->leverancierModel->alle();
        unset($_SESSION['form_data']);

        $formData = !empty($oud) ? $oud : $product;

        $this->view('producten/edit', compact(
            'csrfToken', 'flash', 'product', 'formData',
            'alleLeveranciers'
        ));
    }

    public function wijzigen(): void
    {
        $this->vereisLogin();

        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Ongeldig verzoek (CSRF).');
            $this->redirect('/producten');
        }

        $id     = (int) ($_POST['id'] ?? 0);
        $data   = $this->haalFormDataOp();
        $fouten = $this->valideerProductData($data);

        if (!empty($fouten)) {
            $this->setFlash('error', implode('<br>', $fouten));
            $_SESSION['form_data'] = $data;
            $this->redirect("/producten/wijzigen?id={$id}");
        }

        $fout = $this->productModel->wijzigen($id, $data);

        if ($fout !== '') {
            $this->setFlash('error', $fout);
            $_SESSION['form_data'] = $data;
            $this->redirect("/producten/wijzigen?id={$id}");
        }

        $this->setFlash('success', 'Product succesvol bijgewerkt.');
        $this->redirect('/producten');
    }

    // -------------------------------------------------------
    // Verwijderen
    // -------------------------------------------------------

    public function verwijderen(): void
    {
        $this->vereisLogin();

        if (!$this->valideerCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->logger->warning('CSRF gefaald bij verwijderen product id=' . ($_POST['id'] ?? '?'));
            $this->setFlash('error', 'Sessie verlopen. Probeer de pagina te herladen.');
            $this->redirect('/producten');
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->setFlash('error', 'Ongeldig product-ID.');
            $this->redirect('/producten');
        }

        $fout = $this->productModel->verwijderen($id);

        if ($fout !== '') {
            $this->logger->error("Verwijderen product id={$id} mislukt: {$fout}");
            $this->setFlash('error', 'Verwijderen mislukt: ' . $fout);
        } else {
            $this->logger->info("Product id={$id} succesvol verwijderd.");
            $this->setFlash('success', 'Product succesvol verwijderd.');
        }

        $this->redirect('/producten');
    }

    // -------------------------------------------------------
    // Hulpmethoden
    // -------------------------------------------------------

    /**
     * Lees en saniteer alle formuliervelden uit $_POST.
     *
     * @return array<string,mixed>
     */
    private function haalFormDataOp(): array
    {
        return [
            'productnaam'    => trim($_POST['productnaam'] ?? ''),
            'categorie'      => trim($_POST['categorie'] ?? ''),
            'ean_code'       => trim($_POST['ean_code'] ?? ''),
            'voorraad'       => $_POST['voorraad'] ?? 0,
            'leverancier_id' => $_POST['leverancier_id'] ?? 0,
            'prijs'          => $_POST['prijs'] ?? 0,
        ];
    }

    /**
     * Gedetailleerde server-side validatie.
     *
     * @param  array<string,mixed> $data
     * @return string[]
     */
    private function valideerProductData(array $data): array
    {
        $fouten = [];

        // --- Productnaam ---
        if ($data['productnaam'] === '') {
            $fouten[] = '<strong>Productnaam</strong> is verplicht.';
        } elseif (strlen($data['productnaam']) < 2) {
            $fouten[] = '<strong>Productnaam</strong> moet minimaal 2 tekens bevatten.';
        } elseif (strlen($data['productnaam']) > 150) {
            $fouten[] = '<strong>Productnaam</strong> mag maximaal 150 tekens bevatten.';
        }

        // --- Categorie ---
        if ($data['categorie'] === '') {
            $fouten[] = '<strong>Categorie</strong> is verplicht.';
        } elseif (strlen($data['categorie']) > 100) {
            $fouten[] = '<strong>Categorie</strong> mag maximaal 100 tekens bevatten.';
        }

        // --- EAN-code ---
        if ($data['ean_code'] === '') {
            $fouten[] = '<strong>EAN-code</strong> is verplicht.';
        } elseif (!preg_match('/^[0-9]{13}$/', $data['ean_code'])) {
            $fouten[] = '<strong>EAN-code</strong> moet exact 13 cijfers bevatten.';
        }

        // --- Voorraad ---
        if (!isset($data['voorraad']) || $data['voorraad'] === '' || $data['voorraad'] < 0) {
            $fouten[] = '<strong>Voorraad</strong> moet een positief getal zijn.';
        }

        // --- Leverancier ---
        if (empty($data['leverancier_id'])) {
            $fouten[] = '<strong>Leverancier</strong> is verplicht.';
        }

        // --- Prijs ---
        if (!isset($data['prijs']) || $data['prijs'] === '' || $data['prijs'] < 0) {
            $fouten[] = '<strong>Prijs</strong> moet een positief getal zijn.';
        } elseif (!is_numeric($data['prijs'])) {
            $fouten[] = '<strong>Prijs</strong> moet een geldig getal zijn.';
        }

        return $fouten;
    }
}