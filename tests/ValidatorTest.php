<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests voor de centrale Validator klasse.
 *
 * Test huisnummer, woonplaats, email en postcode validatie.
 */
class ValidatorTest extends TestCase
{
    // ================================================================
    // HUISNUMMER VALIDATIE
    // ================================================================

    /** @test */
    public function testGeldigeHuisnummers(): void
    {
        $geldig = ['1', '12', '99', '141', '4a', '12B', '1000'];
        foreach ($geldig as $h) {
            $this->assertTrue(Validator::isGeldigHuisnummer($h), "Verwacht geldig huisnummer: '{$h}'");
        }
    }

    /** @test */
    public function testOngeligeHuisnummers(): void
    {
        $ongeldig = ['0', '-1', '-99', '-100', 'abc', '', ' '];
        foreach ($ongeldig as $h) {
            $this->assertFalse(Validator::isGeldigHuisnummer($h), "Verwacht ongeldig huisnummer: '{$h}'");
        }
    }

    /** @test */
    public function testNegatievHuisnummerGeeftFout(): void
    {
        $fout = Validator::foutHuisnummer('-5');
        $this->assertNotNull($fout);
        $this->assertStringContainsString('positief', $fout);
    }

    /** @test */
    public function testNulAlsHuisnummerGeeftFout(): void
    {
        $fout = Validator::foutHuisnummer('0');
        $this->assertNotNull($fout);
        $this->assertStringContainsString('positief', $fout);
    }

    /** @test */
    public function testLeegHuisnummerGeeftVerplichtFout(): void
    {
        $fout = Validator::foutHuisnummer('');
        $this->assertNotNull($fout);
        $this->assertStringContainsString('verplicht', $fout);
    }

    /** @test */
    public function testGeldigHuisnummerGeeftGeenFout(): void
    {
        $this->assertNull(Validator::foutHuisnummer('44'));
        $this->assertNull(Validator::foutHuisnummer('141'));
        $this->assertNull(Validator::foutHuisnummer('1'));
    }

    // ================================================================
    // WOONPLAATS VALIDATIE
    // ================================================================

    /** @test */
    public function testBekendeNederlandsePlaatsenZijnGeldig(): void
    {
        $geldig = ['Utrecht', 'Amsterdam', 'Rotterdam', 'Den Haag', 'Eindhoven',
                   'Haarlem', 'Groningen', 'Tilburg', 'Almere', 'Nijmegen'];
        foreach ($geldig as $p) {
            $this->assertTrue(Validator::isGeldigePlaats($p), "Verwacht geldige plaats: '{$p}'");
        }
    }

    /** @test */
    public function testPlaatsValidatieIsCaseInsensitief(): void
    {
        $this->assertTrue(Validator::isGeldigePlaats('utrecht'));
        $this->assertTrue(Validator::isGeldigePlaats('AMSTERDAM'));
        $this->assertTrue(Validator::isGeldigePlaats('den haag'));
    }

    /** @test */
    public function testNietBestaandePlaatsIsOngeldig(): void
    {
        $ongeldig = ['Nergenshuizen', 'Fictiestad', 'XYZdorp', '12345', 'Lorum'];
        foreach ($ongeldig as $p) {
            $this->assertFalse(Validator::isGeldigePlaats($p), "Verwacht ongeldige plaats: '{$p}'");
        }
    }

    /** @test */
    public function testLegePlaatsIsOngeldig(): void
    {
        $this->assertFalse(Validator::isGeldigePlaats(''));
        $this->assertFalse(Validator::isGeldigePlaats('   '));
    }

    /** @test */
    public function testNietBestaandePlaatsGeeftFout(): void
    {
        $fout = Validator::foutPlaats('Nergenshuizen');
        $this->assertNotNull($fout);
        $this->assertStringContainsString('geldige Nederlandse woonplaats', $fout);
    }

    /** @test */
    public function testLegePlaatsGeeftVerplichtFout(): void
    {
        $fout = Validator::foutPlaats('');
        $this->assertNotNull($fout);
        $this->assertStringContainsString('verplicht', $fout);
    }

    /** @test */
    public function testGeldigePlaatsGeeftGeenFout(): void
    {
        $this->assertNull(Validator::foutPlaats('Utrecht'));
        $this->assertNull(Validator::foutPlaats('Amsterdam'));
        $this->assertNull(Validator::foutPlaats('Zwolle'));
    }

    // ================================================================
    // EMAIL VALIDATIE
    // ================================================================

    /** @test */
    public function testGeldigeEmails(): void
    {
        foreach (['jan@example.com', 'lisa@kniploket.nl', 'a+b@test.nl'] as $e) {
            $this->assertTrue(Validator::isGeldigEmail($e));
        }
    }

    /** @test */
    public function testOngeligeEmails(): void
    {
        foreach (['', 'geen-at', '@domein.nl', 'test@'] as $e) {
            $this->assertFalse(Validator::isGeldigEmail($e));
        }
    }

    // ================================================================
    // POSTCODE VALIDATIE
    // ================================================================

    /** @test */
    public function testGeldigePostcodes(): void
    {
        foreach (['3512AB', '1234XY', '3572BC', '3512 AB'] as $p) {
            $this->assertTrue(Validator::isGeldigePostcode($p));
        }
    }

    /** @test */
    public function testOngeligePostcodes(): void
    {
        foreach (['', '123', 'ABCD12', 'AB1234'] as $p) {
            $this->assertFalse(Validator::isGeldigePostcode($p));
        }
    }
}
