<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Models\Product;
use App\Models\Leverancier;

/**
 * Unit tests voor Product model.
 */
class ProductTest extends TestCase
{
    private Product $productModel;
    private Leverancier $leverancierModel;

    protected function setUp(): void
    {
        // Mock de Database en Logger voor tests
        $this->productModel = new Product();
        $this->leverancierModel = new Leverancier();
    }

    /**
     * Test of alle leveranciers opgehaald kunnen worden.
     */
    public function testAlleLeveranciers(): void
    {
        $leveranciers = $this->leverancierModel->alle();
        
        $this->assertIsArray($leveranciers);
        $this->assertNotEmpty($leveranciers);
        $this->assertArrayHasKey('id', $leveranciers[0]);
        $this->assertArrayHasKey('naam', $leveranciers[0]);
    }

    /**
     * Test of een product opgehaald kan worden op ID.
     */
    public function testVindOpId(): void
    {
        // Eerst een product aanmaken om te testen
        $data = [
            'productnaam'    => 'Test Product',
            'categorie'      => 'Test Categorie',
            'ean_code'       => '1234567890123',
            'voorraad'       => 10,
            'leverancier_id' => 1,
            'prijs'          => 9.99
        ];

        $resultaat = $this->productModel->aanmaken($data);
        $this->assertGreaterThan(0, $resultaat['id']);
        $this->assertEquals('', $resultaat['fout']);

        $product = $this->productModel->vindOpId($resultaat['id']);
        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product['productnaam']);
        $this->assertEquals('Test Categorie', $product['categorie']);
        $this->assertEquals('1234567890123', $product['ean_code']);
        $this->assertEquals(10, $product['voorraad']);
        $this->assertEquals(1, $product['leverancier_id']);
        $this->assertEquals('9.99', $product['prijs']);

        // Verwijder test product
        $this->productModel->verwijderen($resultaat['id']);
    }

    /**
     * Test of een product aangemaakt kan worden.
     */
    public function testAanmaken(): void
    {
        $data = [
            'productnaam'    => 'Nieuw Test Product',
            'categorie'      => 'Nieuwe Categorie',
            'ean_code'       => '9876543210987',
            'voorraad'       => 5,
            'leverancier_id' => 2,
            'prijs'          => 19.99
        ];

        $resultaat = $this->productModel->aanmaken($data);
        
        $this->assertGreaterThan(0, $resultaat['id']);
        $this->assertEquals('', $resultaat['fout']);

        // Verwijder test product
        $this->productModel->verwijderen($resultaat['id']);
    }

    /**
     * Test of dubbele productnaam geweigerd wordt.
     */
    public function testDubbeleProductnaam(): void
    {
        $data = [
            'productnaam'    => 'Uniek Product Naam',
            'categorie'      => 'Test',
            'ean_code'       => '1111111111111',
            'voorraad'       => 10,
            'leverancier_id' => 1,
            'prijs'          => 5.00
        ];

        // Eerste aanmaak
        $resultaat1 = $this->productModel->aanmaken($data);
        $this->assertGreaterThan(0, $resultaat1['id']);

        // Tweede aanmaak metzelfde naam
        $resultaat2 = $this->productModel->aanmaken($data);
        $this->assertEquals(0, $resultaat2['id']);
        $this->assertStringContainsString('Productnaam is al in gebruik', $resultaat2['fout']);

        // Verwijder test product
        $this->productModel->verwijderen($resultaat1['id']);
    }

    /**
     * Test of dubbele EAN-code geweigerd wordt.
     */
    public function testDubbeleEanCode(): void
    {
        $data1 = [
            'productnaam'    => 'Product EAN Test 1',
            'categorie'      => 'Test',
            'ean_code'       => '2222222222222',
            'voorraad'       => 10,
            'leverancier_id' => 1,
            'prijs'          => 5.00
        ];

        $data2 = [
            'productnaam'    => 'Product EAN Test 2',
            'categorie'      => 'Test',
            'ean_code'       => '2222222222222', // Zelfde EAN
            'voorraad'       => 10,
            'leverancier_id' => 1,
            'prijs'          => 5.00
        ];

        // Eerste aanmaak
        $resultaat1 = $this->productModel->aanmaken($data1);
        $this->assertGreaterThan(0, $resultaat1['id']);

        // Tweede aanmaak metzelfde EAN
        $resultaat2 = $this->productModel->aanmaken($data2);
        $this->assertEquals(0, $resultaat2['id']);
        $this->assertStringContainsString('EAN-code is al in gebruik', $resultaat2['fout']);

        // Verwijder test product
        $this->productModel->verwijderen($resultaat1['id']);
    }

    /**
     * Test of een product gewijzigd kan worden.
     */
    public function testWijzigen(): void
    {
        // Eerst een product aanmaken
        $data = [
            'productnaam'    => 'Oude Naam',
            'categorie'      => 'Oude Categorie',
            'ean_code'       => '3333333333333',
            'voorraad'       => 10,
            'leverancier_id' => 1,
            'prijs'          => 10.00
        ];

        $resultaat = $this->productModel->aanmaken($data);
        $productId = $resultaat['id'];

        // Wijzig het product
        $updateData = [
            'productnaam'    => 'Nieuwe Naam',
            'categorie'      => 'Nieuwe Categorie',
            'ean_code'       => '3333333333333',
            'voorraad'       => 20,
            'leverancier_id' => 2,
            'prijs'          => 15.50
        ];

        $fout = $this->productModel->wijzigen($productId, $updateData);
        $this->assertEquals('', $fout);

        // Controleer of gewijzigd
        $product = $this->productModel->vindOpId($productId);
        $this->assertEquals('Nieuwe Naam', $product['productnaam']);
        $this->assertEquals('Nieuwe Categorie', $product['categorie']);
        $this->assertEquals(20, $product['voorraad']);
        $this->assertEquals(2, $product['leverancier_id']);
        $this->assertEquals('15.50', $product['prijs']);

        // Verwijder test product
        $this->productModel->verwijderen($productId);
    }

    /**
     * Test of een product verwijderd kan worden.
     */
    public function testVerwijderen(): void
    {
        // Eerst een product aanmaken
        $data = [
            'productnaam'    => 'Te Verwijderen Product',
            'categorie'      => 'Test',
            'ean_code'       => '4444444444444',
            'voorraad'       => 5,
            'leverancier_id' => 1,
            'prijs'          => 7.50
        ];

        $resultaat = $this->productModel->aanmaken($data);
        $productId = $resultaat['id'];

        // Verwijder het product
        $fout = $this->productModel->verwijderen($productId);
        $this->assertEquals('', $fout);

        // Controleer of het echt weg is
        $product = $this->productModel->vindOpId($productId);
        $this->assertNull($product);
    }

    /**
     * Test of statistieken opgehaald kunnen worden.
     */
    public function testStatistieken(): void
    {
        $stats = $this->productModel->statistieken();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('aantal_producten', $stats);
        $this->assertArrayHasKey('producten_uitverkocht', $stats);
        $this->assertArrayHasKey('aantal_leveranciers', $stats);
        $this->assertGreaterThanOrEqual(0, $stats['aantal_producten']);
    }
}