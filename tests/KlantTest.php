<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Models\Klant;
use App\Models\Allergeen;

/**
 * Unit tests voor Klant model.
 */
class KlantTest extends TestCase
{
    private Klant $klantModel;
    private Allergeen $allergeenModel;

    protected function setUp(): void
    {
        // Mock de Database en Logger voor tests
        $this->klantModel = new Klant();
        $this->allergeenModel = new Allergeen();
    }

    /**
     * Test of alle allergenen opgehaald kunnen worden.
     */
    public function testAlleAllergenen(): void
    {
        $allergenen = $this->allergeenModel->alle();
        
        $this->assertIsArray($allergenen);
        $this->assertNotEmpty($allergenen);
        $this->assertArrayHasKey('id', $allergenen[0]);
        $this->assertArrayHasKey('naam', $allergenen[0]);
    }

    /**
     * Test of alle klanten opgehaald kunnen worden.
     */
    public function testOverzicht(): void
    {
        $klanten = $this->klantModel->overzicht();
        
        $this->assertIsArray($klanten);
        $this->assertArrayHasKey('id', $klanten[0]);
        $this->assertArrayHasKey('naam', $klanten[0]);
        $this->assertArrayHasKey('email', $klanten[0]);
    }

    /**
     * Test of een klant opgehaald kan worden op ID.
     */
    public function testVindOpId(): void
    {
        // Eerst een klant aanmaken om te testen
        $data = [
            'naam'      => 'Test Klant',
            'email'     => 'test.klant@example.com',
            'wachtwoord' => 'Test123!',
            'adres'     => 'Teststraat 1',
            'telefoonnummer' => '0612345678',
            'wensen'    => 'Test wensen',
            'allergenen' => []
        ];

        $resultaat = $this->klantModel->aanmaken($data);
        $this->assertGreaterThan(0, $resultaat['id']);
        $this->assertEquals('', $resultaat['fout']);

        $klant = $this->klantModel->vindOpId($resultaat['id']);
        $this->assertNotNull($klant);
        $this->assertEquals('Test Klant', $klant['naam']);
        $this->assertEquals('test.klant@example.com', $klant['email']);
        $this->assertEquals('Teststraat 1', $klant['adres']);
        $this->assertEquals('0612345678', $klant['telefoonnummer']);

        // Verwijder test klant
        $this->klantModel->verwijderen($resultaat['id']);
    }

    /**
     * Test of een klant aangemaakt kan worden.
     */
    public function testAanmaken(): void
    {
        $data = [
            'naam'      => 'Nieuwe Test Klant',
            'email'     => 'nieuwe.klant@example.com',
            'wachtwoord' => 'Nieuw123!',
            'adres'     => 'Nieuwe Straat 2',
            'telefoonnummer' => '0687654321',
            'wensen'    => 'Nieuwe wensen',
            'allergenen' => [1, 2]
        ];

        $resultaat = $this->klantModel->aanmaken($data);
        
        $this->assertGreaterThan(0, $resultaat['id']);
        $this->assertEquals('', $resultaat['fout']);

        // Verwijder test klant
        $this->klantModel->verwijderen($resultaat['id']);
    }

    /**
     * Test of dubbele e-mail geweigerd wordt.
     */
    public function testDubbeleEmail(): void
    {
        $data = [
            'naam'      => 'Unieke Klant',
            'email'     => 'uniek.email@example.com',
            'wachtwoord' => 'Uniek123!',
            'adres'     => 'Uniek 3',
            'telefoonnummer' => '0611111111',
            'wensen'    => '',
            'allergenen' => []
        ];

        // Eerste aanmaak
        $resultaat1 = $this->klantModel->aanmaken($data);
        $this->assertGreaterThan(0, $resultaat1['id']);

        // Tweede aanmaak metzelfde e-mail
        $resultaat2 = $this->klantModel->aanmaken($data);
        $this->assertEquals(0, $resultaat2['id']);
        $this->assertStringContainsString('E-mailadres is al in gebruik', $resultaat2['fout']);

        // Verwijder test klant
        $this->klantModel->verwijderen($resultaat1['id']);
    }

    /**
     * Test of een klant gewijzigd kan worden.
     */
    public function testWijzigen(): void
    {
        // Eerst een klant aanmaken
        $data = [
            'naam'      => 'Oude Naam',
            'email'     => 'oude.email@example.com',
            'wachtwoord' => 'Oude123!',
            'adres'     => 'Oude Straat 4',
            'telefoonnummer' => '0622222222',
            'wensen'    => 'Oude wensen',
            'allergenen' => []
        ];

        $resultaat = $this->klantModel->aanmaken($data);
        $klantId = $resultaat['id'];

        // Wijzig de klant
        $updateData = [
            'naam'      => 'Nieuwe Naam',
            'email'     => 'nieuwe.email@example.com',
            'wachtwoord' => 'Nieuwe123!',
            'adres'     => 'Nieuwe Straat 5',
            'telefoonnummer' => '0633333333',
            'wensen'    => 'Nieuwe wensen',
            'allergenen' => [3, 4]
        ];

        $fout = $this->klantModel->wijzigen($klantId, $updateData);
        $this->assertEquals('', $fout);

        // Controleer of gewijzigd
        $klant = $this->klantModel->vindOpId($klantId);
        $this->assertEquals('Nieuwe Naam', $klant['naam']);
        $this->assertEquals('nieuwe.email@example.com', $klant['email']);
        $this->assertEquals('Nieuwe Straat 5', $klant['adres']);
        $this->assertEquals('0633333333', $klant['telefoonnummer']);

        // Verwijder test klant
        $this->klantModel->verwijderen($klantId);
    }

    /**
     * Test of een klant verwijderd kan worden.
     */
    public function testVerwijderen(): void
    {
        // Eerst een klant aanmaken
        $data = [
            'naam'      => 'Te Verwijderen Klant',
            'email'     => 'verwijderen@example.com',
            'wachtwoord' => 'Verwijder123!',
            'adres'     => 'Verwijder Straat 6',
            'telefoonnummer' => '0644444444',
            'wensen'    => '',
            'allergenen' => []
        ];

        $resultaat = $this->klantModel->aanmaken($data);
        $klantId = $resultaat['id'];

        // Verwijder de klant
        $fout = $this->klantModel->verwijderen($klantId);
        $this->assertEquals('', $fout);

        // Controleer of het echt weg is
        $klant = $this->klantModel->vindOpId($klantId);
        $this->assertNull($klant);
    }

    /**
     * Test of statistieken opgehaald kunnen worden.
     */
    public function testStatistieken(): void
    {
        $stats = $this->klantModel->statistieken();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('aantal_klanten', $stats);
        $this->assertArrayHasKey('geplande_afspraken', $stats);
        $this->assertArrayHasKey('aantal_medewerkers', $stats);
        $this->assertArrayHasKey('producten_uitverkocht', $stats);
        $this->assertGreaterThanOrEqual(0, $stats['aantal_klanten']);
    }

    /**
     * Test of allergenen van een klant opgehaald kunnen worden.
     */
    public function testAllergenenVanKlant(): void
    {
        // Eerst een klant aanmaken met allergenen
        $data = [
            'naam'      => 'Klant Met Allergenen',
            'email'     => 'allergeen@example.com',
            'wachtwoord' => 'Allergeen123!',
            'adres'     => 'Allergen Straat 7',
            'telefoonnummer' => '0655555555',
            'wensen'    => '',
            'allergenen' => [1, 5, 10]
        ];

        $resultaat = $this->klantModel->aanmaken($data);
        $klantId = $resultaat['id'];

        // Haal allergenen op
        $allergenen = $this->allergeenModel->namenVanKlant($klantId);
        
        $this->assertIsArray($allergenen);
        $this->assertNotEmpty($allergenen);
        $this->assertContains('Gluten', $allergenen);
        $this->assertContains('Pinda', $allergenen);
        $this->assertContains('Mosterd', $allergenen);

        // Verwijder test klant
        $this->klantModel->verwijderen($klantId);
    }
}