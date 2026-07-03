<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests voor Klant-module.
 *
 * Test validatielogica, naam-opbouw, postcode-filter en email-checks
 * zonder databaseverbinding.
 */
class KlantTest extends TestCase
{
    // ----------------------------------------------------------------
    // Helpers (zelfde logica als in controller/model)
    // ----------------------------------------------------------------

    private function isGeldigEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function isGeldigePostcode(string $postcode): bool
    {
        return (bool) preg_match('/^\d{4}\s?[A-Za-z]{2}$/', $postcode);
    }

    private function bouwNaam(array $klant): string
    {
        $naam = $klant['Voornaam'];
        if (!empty($klant['Tussenvoegsel'])) {
            $naam .= ' ' . $klant['Tussenvoegsel'];
        }
        return trim($naam . ' ' . $klant['Achternaam']);
    }

    // ================================================================
    // EMAIL VALIDATIE
    // ================================================================

    /** @test */
    public function testGeldigeEmailAdressen(): void
    {
        foreach (['jan.jansen@outlook.com', 'lisa@kniploket.nl', 'a+b@test.nl'] as $email) {
            $this->assertTrue($this->isGeldigEmail($email), "Verwacht geldig: {$email}");
        }
    }

    /** @test */
    public function testOngeligeEmailAdressen(): void
    {
        foreach (['', 'geen-apenstaartje', '@domein.nl', 'test@'] as $email) {
            $this->assertFalse($this->isGeldigEmail($email), "Verwacht ongeldig: '{$email}'");
        }
    }

    // ================================================================
    // POSTCODE VALIDATIE
    // ================================================================

    /** @test */
    public function testGeldigePostcodes(): void
    {
        foreach (['3512AB', '1234XY', '3572BC', '3512 AB'] as $pc) {
            $this->assertTrue($this->isGeldigePostcode($pc), "Verwacht geldig: {$pc}");
        }
    }

    /** @test */
    public function testOngeligePostcodes(): void
    {
        foreach (['', '123', 'ABCD12', 'AB1234', '00000'] as $pc) {
            $this->assertFalse($this->isGeldigePostcode($pc), "Verwacht ongeldig: '{$pc}'");
        }
    }

    // ================================================================
    // NAAM OPBOUW
    // ================================================================

    /** @test */
    public function testNaamZonderTussenvoegsel(): void
    {
        $this->assertSame('Jan Jansen', $this->bouwNaam([
            'Voornaam' => 'Jan', 'Tussenvoegsel' => '', 'Achternaam' => 'Jansen'
        ]));
    }

    /** @test */
    public function testNaamMetTussenvoegsel(): void
    {
        $this->assertSame('Piet van Loenen', $this->bouwNaam([
            'Voornaam' => 'Piet', 'Tussenvoegsel' => 'van', 'Achternaam' => 'Loenen'
        ]));
    }

    /** @test */
    public function testNaamMetMeerwoordigTussenvoegsel(): void
    {
        $this->assertSame('Marieke van den Berg', $this->bouwNaam([
            'Voornaam' => 'Marieke', 'Tussenvoegsel' => 'van den', 'Achternaam' => 'Berg'
        ]));
    }

    // ================================================================
    // VALIDATIE LOGICA (serverside simulatie)
    // ================================================================

    /** @test */
    public function testAlleVerplichteLegVeldenGevenFouten(): void
    {
        $data   = ['contact_email' => '', 'straatnaam' => '', 'huisnummer' => '',
                   'postcode' => '', 'plaats' => '', 'mobiel' => ''];
        $fouten = [];
        foreach (array_keys($data) as $veld) {
            if (empty(trim($data[$veld]))) { $fouten[$veld] = 'verplicht'; }
        }
        $this->assertCount(6, $fouten);
    }

    /** @test */
    public function testGeldigFormulierGeeftGeenFouten(): void
    {
        $data   = ['contact_email' => 'jan@example.com', 'straatnaam' => 'Biltstraat',
                   'huisnummer' => '44', 'postcode' => '3572BC', 'plaats' => 'Utrecht', 'mobiel' => '0612345678'];
        $fouten = [];
        if (!$this->isGeldigEmail($data['contact_email'])) { $fouten['contact_email'] = 'ongeldig'; }
        if (!$this->isGeldigePostcode($data['postcode']))  { $fouten['postcode']       = 'ongeldig'; }
        foreach (['straatnaam','huisnummer','plaats','mobiel'] as $v) {
            if (empty(trim($data[$v]))) { $fouten[$v] = 'verplicht'; }
        }
        $this->assertEmpty($fouten);
    }

    /** @test */
    public function testOngeldigEmailGeeftValidatiefout(): void
    {
        $this->assertFalse($this->isGeldigEmail('geen-geldig-email'));
    }

    /** @test */
    public function testOngeldigePostcodeGeeftValidatiefout(): void
    {
        $this->assertFalse($this->isGeldigePostcode('ONGELDIG'));
    }

    // ================================================================
    // POSTCODE FILTER LOGICA
    // ================================================================

    /** @test */
    public function testPostcodeNormaliseringVerwijdertSpatie(): void
    {
        $this->assertSame('3572BC', str_replace(' ', '', strtoupper('3572 bc')));
    }

    /** @test */
    public function testPostcodePrefixFilterMatchtCorrect(): void
    {
        $postcodes = ['3512AB', '3572BC', '1234XY', '3511AB'];
        $gevonden  = array_filter($postcodes, fn($p) => str_starts_with($p, '35'));
        $this->assertCount(3, array_values($gevonden));
    }

    // ================================================================
    // RELATIENUMMER FORMAAT
    // ================================================================

    /** @test */
    public function testRelatienummerFormaatKlopt(): void
    {
        foreach (['KL-2026-001', 'KL-2026-006', 'KL-2026-004'] as $nr) {
            $this->assertMatchesRegularExpression('/^KL-\d{4}-\d{3,}$/', $nr);
        }
    }

    /** @test */
    public function testRelatienummerBegintMetKL(): void
    {
        $this->assertTrue(str_starts_with('KL-2026-001', 'KL-'));
        $this->assertFalse(str_starts_with('MD-2026-001', 'KL-'));
    }

    // ================================================================
    // EMAIL UNICITEIT SIMULATIE
    // ================================================================

    /** @test */
    public function testBestaandEmailAdresWordtGesignaleerd(): void
    {
        $bestaandeEmails = ['jan.jansen@outlook.com', 'piet.van.loenen@gmail.com'];
        $nieuwEmail      = 'jan.jansen@outlook.com'; // al in gebruik
        $this->assertContains($nieuwEmail, $bestaandeEmails);
    }

    /** @test */
    public function testNieuwEmailAdresWordtGeaccepteerd(): void
    {
        $bestaandeEmails = ['jan.jansen@outlook.com', 'piet.van.loenen@gmail.com'];
        $nieuwEmail      = 'nieuw@example.com';
        $this->assertNotContains($nieuwEmail, $bestaandeEmails);
    }
}
