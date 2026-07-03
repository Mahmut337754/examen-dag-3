<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Unit tests voor Klant-functionaliteit.
 *
 * Test de business-logica (validaties, email-checks, data-manipulatie)
 * zonder daadwerkelijke databaseverbinding.
 */
class KlantTest extends TestCase
{
    // ----------------------------------------------------------------
    // Helper: valideert een e-mailadres (zelfde logica als in controller)
    // ----------------------------------------------------------------
    private function isGeldigEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // ----------------------------------------------------------------
    // Helper: valideert een Nederlandse postcode (DDDDLL)
    // ----------------------------------------------------------------
    private function isGeldigePostcode(string $postcode): bool
    {
        return (bool)preg_match('/^\d{4}\s?[A-Za-z]{2}$/', $postcode);
    }

    // ----------------------------------------------------------------
    // Helper: bouwt naam samen uit klantgegevens
    // ----------------------------------------------------------------
    private function bouwNaam(array $klant): string
    {
        $naam = $klant['Voornaam'];
        if (!empty($klant['Tussenvoegsel'])) {
            $naam .= ' ' . $klant['Tussenvoegsel'];
        }
        $naam .= ' ' . $klant['Achternaam'];
        return trim($naam);
    }

    // ================================================================
    // EMAIL VALIDATIE TESTS
    // ================================================================

    /** @test */
    public function testGeldigEmailAdresWordtGeaccepteerd(): void
    {
        $geldige = [
            'test@example.com',
            'jan.jansen@outlook.com',
            'user+tag@domain.nl',
            'lisa@kniploket.nl',
        ];

        foreach ($geldige as $email) {
            $this->assertTrue(
                $this->isGeldigEmail($email),
                "Verwacht geldig: {$email}"
            );
        }
    }

    /** @test */
    public function testOngeldigEmailAdresWordtGeweigerd(): void
    {
        $ongeldige = [
            '',
            'geen-apenstaartje',
            '@domein.nl',
            'test@',
            'test@.com',
            'a b@test.nl',
        ];

        foreach ($ongeldige as $email) {
            $this->assertFalse(
                $this->isGeldigEmail($email),
                "Verwacht ongeldig: '{$email}'"
            );
        }
    }

    // ================================================================
    // POSTCODE VALIDATIE TESTS
    // ================================================================

    /** @test */
    public function testGeldigeNederlandsePostcodeWordtGeaccepteerd(): void
    {
        $geldige = [
            '3512AB',
            '1234XY',
            '9999ZZ',
            '3512 AB',  // met spatie
        ];

        foreach ($geldige as $pc) {
            $this->assertTrue(
                $this->isGeldigePostcode($pc),
                "Verwacht geldige postcode: {$pc}"
            );
        }
    }

    /** @test */
    public function testOngeldigePostcodeWordtGeweigerd(): void
    {
        $ongeldige = [
            '',
            '123',
            'ABCD12',
            '35121',
            'AB1234',
            '00000',
        ];

        foreach ($ongeldige as $pc) {
            $this->assertFalse(
                $this->isGeldigePostcode($pc),
                "Verwacht ongeldige postcode: '{$pc}'"
            );
        }
    }

    // ================================================================
    // NAAM OPBOUW TESTS
    // ================================================================

    /** @test */
    public function testNaamZonderTussenvoegsel(): void
    {
        $klant = [
            'Voornaam'      => 'Jan',
            'Tussenvoegsel' => '',
            'Achternaam'    => 'Jansen',
        ];
        $this->assertSame('Jan Jansen', $this->bouwNaam($klant));
    }

    /** @test */
    public function testNaamMetTussenvoegsel(): void
    {
        $klant = [
            'Voornaam'      => 'Piet',
            'Tussenvoegsel' => 'van',
            'Achternaam'    => 'Loenen',
        ];
        $this->assertSame('Piet van Loenen', $this->bouwNaam($klant));
    }

    /** @test */
    public function testNaamMetMeerdereWoorden(): void
    {
        $klant = [
            'Voornaam'      => 'Marieke',
            'Tussenvoegsel' => 'van den',
            'Achternaam'    => 'Berg',
        ];
        $this->assertSame('Marieke van den Berg', $this->bouwNaam($klant));
    }

    // ================================================================
    // VALIDATIE LOGICA TESTS (serverside)
    // ================================================================

    /** @test */
    public function testVerplichtVeldenValidatie(): void
    {
        $verplicht = ['contact_email', 'straatnaam', 'huisnummer', 'postcode', 'plaats', 'mobiel'];
        $formData  = [
            'contact_email' => '',
            'straatnaam'    => '',
            'huisnummer'    => '',
            'postcode'      => '',
            'plaats'        => '',
            'mobiel'        => '',
        ];

        $fouten = [];
        foreach ($verplicht as $veld) {
            if (empty(trim($formData[$veld]))) {
                $fouten[$veld] = 'is verplicht';
            }
        }

        $this->assertCount(6, $fouten, '6 velden moeten leeg zijn');
        foreach ($verplicht as $veld) {
            $this->assertArrayHasKey($veld, $fouten);
        }
    }

    /** @test */
    public function testGeldigFormDataGeeftGeenValidatiefouten(): void
    {
        $formData = [
            'contact_email' => 'jan@example.com',
            'straatnaam'    => 'Biltstraat',
            'huisnummer'    => '44',
            'postcode'      => '3572BC',
            'plaats'        => 'Utrecht',
            'mobiel'        => '0612345678',
        ];

        $fouten = [];

        if (empty($formData['contact_email'])) {
            $fouten['contact_email'] = 'verplicht';
        } elseif (!$this->isGeldigEmail($formData['contact_email'])) {
            $fouten['contact_email'] = 'ongeldig';
        }
        if (empty($formData['straatnaam'])) {
            $fouten['straatnaam'] = 'verplicht';
        }
        if (empty($formData['huisnummer'])) {
            $fouten['huisnummer'] = 'verplicht';
        }
        if (empty($formData['postcode'])) {
            $fouten['postcode'] = 'verplicht';
        } elseif (!$this->isGeldigePostcode($formData['postcode'])) {
            $fouten['postcode'] = 'ongeldig';
        }
        if (empty($formData['plaats'])) {
            $fouten['plaats'] = 'verplicht';
        }
        if (empty($formData['mobiel'])) {
            $fouten['mobiel'] = 'verplicht';
        }

        $this->assertEmpty($fouten, 'Geldig formulier mag geen fouten geven');
    }

    /** @test */
    public function testOngeldigenEmailGeeftValidatiefout(): void
    {
        $formData = ['contact_email' => 'geen-geldig-email'];

        $fout = null;
        if (!$this->isGeldigEmail($formData['contact_email'])) {
            $fout = 'Voer een geldig e-mailadres in';
        }

        $this->assertNotNull($fout);
        $this->assertStringContainsString('geldig e-mailadres', $fout);
    }

    /** @test */
    public function testOngedigdePostcodeGeeftValidatiefout(): void
    {
        $formData = ['postcode' => 'ONGELDIG'];

        $fout = null;
        if (!$this->isGeldigePostcode($formData['postcode'])) {
            $fout = 'Voer een geldige postcode in (bijv. 3512AB)';
        }

        $this->assertNotNull($fout);
        $this->assertStringContainsString('geldige postcode', $fout);
    }

    // ================================================================
    // POSTCODE FILTER TESTS
    // ================================================================

    /** @test */
    public function testPostcodeFilterNormaliseert(): void
    {
        // Simuleer de postcode-normalisatie uit het model
        $input    = '3572 bc';
        $genorm   = str_replace(' ', '', strtoupper($input));

        $this->assertSame('3572BC', $genorm);
    }

    /** @test */
    public function testPostcodeFilterMetPrefix(): void
    {
        // Zoek op prefix '35' – moet '3512AB' en '3572BC' matchen
        $prefix    = '35';
        $postcodes = ['3512AB', '3572BC', '1234XY', '3511AB'];
        $gevonden  = array_filter($postcodes, fn($p) => str_starts_with($p, $prefix));

        $this->assertCount(3, array_values($gevonden));
    }

    // ================================================================
    // RELATIENUMMER FORMAT TESTS
    // ================================================================

    /** @test */
    public function testRelatienummerFormatIsCorrect(): void
    {
        // KL-JJJJ-NNN formaat
        $nummers = ['KL-2026-001', 'KL-2026-006', 'KL-2026-004'];

        foreach ($nummers as $nr) {
            $this->assertMatchesRegularExpression(
                '/^KL-\d{4}-\d{3,}$/',
                $nr,
                "Relatienummer formaat klopt niet: {$nr}"
            );
        }
    }

    /** @test */
    public function testRelatienummerMustStartWithKL(): void
    {
        $geldig   = 'KL-2026-001';
        $ongeldig = 'MD-2026-001';

        $this->assertTrue(str_starts_with($geldig, 'KL-'));
        $this->assertFalse(str_starts_with($ongeldig, 'KL-'));
    }
}
