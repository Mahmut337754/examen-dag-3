<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests voor Medewerker-functionaliteit.
 *
 * Test de business-logica (filters, specialisaties, paginering, naam-opbouw)
 * zonder daadwerkelijke databaseverbinding.
 */
class MedewerkerTest extends TestCase
{
    // ----------------------------------------------------------------
    // Testdata – simulaties van database-resultaten
    // ----------------------------------------------------------------
    private array $testMedewerkers = [
        [
            'Id' => 1, 'Voornaam' => 'Fatima', 'Tussenvoegsel' => null,
            'Achternaam' => 'El Amrani', 'Specialisatie' => 'Knippen',
            'Straatnaam' => 'Kanaalstraat', 'Huisnummer' => '12', 'Toevoeging' => null,
            'Postcode' => '3511AB', 'Plaats' => 'Utrecht',
            'Mobiel' => '0612345678', 'ContactEmail' => 'fatima@kniplokettiko.nl',
        ],
        [
            'Id' => 2, 'Voornaam' => 'Sanne', 'Tussenvoegsel' => 'de',
            'Achternaam' => 'Vries', 'Specialisatie' => 'Kleuren',
            'Straatnaam' => 'Croeselaan', 'Huisnummer' => '101', 'Toevoeging' => null,
            'Postcode' => '3521BJ', 'Plaats' => 'Utrecht',
            'Mobiel' => '0611111111', 'ContactEmail' => 'sanne.devries@kniplokettiko.nl',
        ],
        [
            'Id' => 7, 'Voornaam' => 'Kevin', 'Tussenvoegsel' => null,
            'Achternaam' => 'Smit', 'Specialisatie' => 'Extensions',
            'Straatnaam' => 'Bernardlaan', 'Huisnummer' => '7', 'Toevoeging' => null,
            'Postcode' => '3527GA', 'Plaats' => 'Utrecht',
            'Mobiel' => '0611111116', 'ContactEmail' => 'kevin.smit@kniplokettiko.nl',
        ],
        [
            'Id' => 4, 'Voornaam' => 'Lisa', 'Tussenvoegsel' => 'van',
            'Achternaam' => 'Dijk', 'Specialisatie' => 'Stylen',
            'Straatnaam' => 'Maliebaan', 'Huisnummer' => '17', 'Toevoeging' => null,
            'Postcode' => '3581CC', 'Plaats' => 'Utrecht',
            'Mobiel' => '0611111113', 'ContactEmail' => 'lisa.vandijk@kniplokettiko.nl',
        ],
        [
            'Id' => 8, 'Voornaam' => 'Aylin', 'Tussenvoegsel' => null,
            'Achternaam' => 'Demir', 'Specialisatie' => 'Stylen',
            'Straatnaam' => 'Laan van Nieuw-Guinea', 'Huisnummer' => '141', 'Toevoeging' => null,
            'Postcode' => '3531JE', 'Plaats' => 'Utrecht',
            'Mobiel' => '0611111117', 'ContactEmail' => 'aylin.demir@kniplokettiko.nl',
        ],
    ];

    // ----------------------------------------------------------------
    // Helper: bouwt volledige naam samen
    // ----------------------------------------------------------------
    private function bouwNaam(array $medewerker): string
    {
        $naam = $medewerker['Voornaam'];
        if (!empty($medewerker['Tussenvoegsel'])) {
            $naam .= ' ' . $medewerker['Tussenvoegsel'];
        }
        $naam .= ' ' . $medewerker['Achternaam'];
        return trim($naam);
    }

    // ----------------------------------------------------------------
    // Helper: filtert medewerkers op specialisatie
    // ----------------------------------------------------------------
    private function filterOpSpecialisatie(array $medewerkers, ?string $specialisatie): array
    {
        if ($specialisatie === null || $specialisatie === '') {
            return $medewerkers;
        }
        return array_values(array_filter(
            $medewerkers,
            fn($m) => $m['Specialisatie'] === $specialisatie
        ));
    }

    // ================================================================
    // NAAM OPBOUW TESTS
    // ================================================================

    /** @test */
    public function testNaamZonderTussenvoegsel(): void
    {
        $medewerker = [
            'Voornaam' => 'Fatima', 'Tussenvoegsel' => null, 'Achternaam' => 'El Amrani'
        ];
        $this->assertSame('Fatima El Amrani', $this->bouwNaam($medewerker));
    }

    /** @test */
    public function testNaamMetTussenvoegsel(): void
    {
        $medewerker = [
            'Voornaam' => 'Sanne', 'Tussenvoegsel' => 'de', 'Achternaam' => 'Vries'
        ];
        $this->assertSame('Sanne de Vries', $this->bouwNaam($medewerker));
    }

    /** @test */
    public function testNaamMetLangTussenvoegsel(): void
    {
        $medewerker = [
            'Voornaam' => 'Lisa', 'Tussenvoegsel' => 'van', 'Achternaam' => 'Dijk'
        ];
        $this->assertSame('Lisa van Dijk', $this->bouwNaam($medewerker));
    }

    /** @test */
    public function testNaamMetLeegStringTussenvoegsel(): void
    {
        $medewerker = [
            'Voornaam' => 'Kevin', 'Tussenvoegsel' => '', 'Achternaam' => 'Smit'
        ];
        $this->assertSame('Kevin Smit', $this->bouwNaam($medewerker));
    }

    // ================================================================
    // SPECIALISATIE FILTER TESTS
    // ================================================================

    /** @test */
    public function testFilterOpKnippen(): void
    {
        $gefilterd = $this->filterOpSpecialisatie($this->testMedewerkers, 'Knippen');
        $this->assertCount(1, $gefilterd);
        $this->assertSame('Knippen', $gefilterd[0]['Specialisatie']);
    }

    /** @test */
    public function testFilterOpStylen(): void
    {
        $gefilterd = $this->filterOpSpecialisatie($this->testMedewerkers, 'Stylen');
        $this->assertCount(2, $gefilterd);
        foreach ($gefilterd as $m) {
            $this->assertSame('Stylen', $m['Specialisatie']);
        }
    }

    /** @test */
    public function testFilterOpNietBestaandeSpecialisatieGeeftLeegResultaat(): void
    {
        $gefilterd = $this->filterOpSpecialisatie($this->testMedewerkers, 'Permanent');
        $this->assertEmpty($gefilterd);
        $this->assertCount(0, $gefilterd);
    }

    /** @test */
    public function testGeenFilterGeeftAlleMedewerkers(): void
    {
        $gefilterd = $this->filterOpSpecialisatie($this->testMedewerkers, null);
        $this->assertCount(5, $gefilterd);
    }

    /** @test */
    public function testLeegStringFilterGeeftAlleMedewerkers(): void
    {
        $gefilterd = $this->filterOpSpecialisatie($this->testMedewerkers, '');
        $this->assertCount(5, $gefilterd);
    }

    // ================================================================
    // UNIEKE SPECIALISATIES TESTS
    // ================================================================

    /** @test */
    public function testUniekeSpecialisatiesWordenOpgehaald(): void
    {
        $alle         = array_column($this->testMedewerkers, 'Specialisatie');
        $uniek        = array_unique($alle);
        sort($uniek);

        $this->assertContains('Knippen',    $uniek);
        $this->assertContains('Kleuren',    $uniek);
        $this->assertContains('Extensions', $uniek);
        $this->assertContains('Stylen',     $uniek);
        $this->assertCount(4, $uniek);
    }

    /** @test */
    public function testSpecialisatiesZijnGesorteerd(): void
    {
        $alle  = array_unique(array_column($this->testMedewerkers, 'Specialisatie'));
        sort($alle);

        // Eerste alphabetisch moet Extensions zijn
        $this->assertSame('Extensions', $alle[0]);
    }

    // ================================================================
    // PAGINERING TESTS
    // ================================================================

    /** @test */
    public function testPagineringBerekeningCorrect(): void
    {
        $totaal    = 10;
        $perPagina = 4;

        $totaalPaginas = (int)ceil($totaal / $perPagina);
        $this->assertSame(3, $totaalPaginas);
    }

    /** @test */
    public function testEerstePaginaOffsetIsNul(): void
    {
        $huidigePagina = 1;
        $perPagina     = 4;
        $offset        = ($huidigePagina - 1) * $perPagina;

        $this->assertSame(0, $offset);
    }

    /** @test */
    public function testTweedePaginaOffsetIsCorrect(): void
    {
        $huidigePagina = 2;
        $perPagina     = 4;
        $offset        = ($huidigePagina - 1) * $perPagina;

        $this->assertSame(4, $offset);
    }

    /** @test */
    public function testPaginaNietGroterDanTotaalPaginas(): void
    {
        $totaal        = 5;
        $perPagina     = 4;
        $totaalPaginas = max(1, (int)ceil($totaal / $perPagina));

        // Gevraagde pagina 99 moet worden begrensd
        $gevraagd      = 99;
        $huidigePagina = min($gevraagd, $totaalPaginas);

        $this->assertSame($totaalPaginas, $huidigePagina);
    }

    /** @test */
    public function testLeegResultaatGeeftAlsnogEenPagina(): void
    {
        $totaal        = 0;
        $perPagina     = 4;
        $totaalPaginas = max(1, (int)ceil($totaal / $perPagina));

        $this->assertSame(1, $totaalPaginas);
    }

    // ================================================================
    // ADRES OPBOUW TESTS
    // ================================================================

    /** @test */
    public function testAdresZonderToevoeging(): void
    {
        $m     = $this->testMedewerkers[0]; // Fatima
        $adres = trim(($m['Straatnaam'] ?? '') . ' ' . ($m['Huisnummer'] ?? '') . ($m['Toevoeging'] ?? ''));

        $this->assertSame('Kanaalstraat 12', $adres);
    }

    /** @test */
    public function testAdresMetLangeStraatNaam(): void
    {
        $m     = $this->testMedewerkers[4]; // Aylin – Laan van Nieuw-Guinea 141
        $adres = trim(($m['Straatnaam'] ?? '') . ' ' . ($m['Huisnummer'] ?? '') . ($m['Toevoeging'] ?? ''));

        $this->assertSame('Laan van Nieuw-Guinea 141', $adres);
    }

    // ================================================================
    // CONTACT E-MAIL TESTS
    // ================================================================

    /** @test */
    public function testContactEmailIsBeschikbaar(): void
    {
        foreach ($this->testMedewerkers as $m) {
            $this->assertNotEmpty($m['ContactEmail']);
            $this->assertStringContainsString('@', $m['ContactEmail']);
        }
    }

    /** @test */
    public function testAlleContactEmailsZijnGeldig(): void
    {
        foreach ($this->testMedewerkers as $m) {
            $this->assertNotFalse(
                filter_var($m['ContactEmail'], FILTER_VALIDATE_EMAIL),
                "Ongeldig contact e-mail: {$m['ContactEmail']}"
            );
        }
    }

    // ================================================================
    // GEEN RESULTAAT SCENARIO TEST
    // ================================================================

    /** @test */
    public function testGeenResultaatBijNietBestaandeSpecialisatieGeeftCorrecteVlag(): void
    {
        $specialisatie = 'Permanent';
        $gefilterd     = $this->filterOpSpecialisatie($this->testMedewerkers, $specialisatie);

        $filterActief  = $specialisatie !== '' && $specialisatie !== 'Alle specialisaties';
        $geenResultaat = $filterActief && count($gefilterd) === 0;

        $this->assertTrue($geenResultaat);
    }

    /** @test */
    public function testAlleSpecialisatiesFilterWordtAlsInactiefBeschouwd(): void
    {
        $specialisatie = 'Alle specialisaties';
        $filterActief  = $specialisatie !== '' && $specialisatie !== 'Alle specialisaties';

        $this->assertFalse($filterActief);
    }
}
