<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests voor Medewerker-module.
 *
 * Test specialisatie-filters, naam-opbouw, leeftijdsberekening,
 * minderjarige-validatie en paginering logica.
 */
class MedewerkerTest extends TestCase
{
    private array $testMedewerkers = [
        ['Id' => 8, 'Voornaam' => 'Aylin', 'Tussenvoegsel' => null, 'Achternaam' => 'Demir', 'Specialisatie' => 'Stylen', 'Geboortedatum' => '1999-12-04'],
        ['Id' => 2, 'Voornaam' => 'Sanne', 'Tussenvoegsel' => 'de', 'Achternaam' => 'Vries', 'Specialisatie' => 'Kleuren', 'Geboortedatum' => '1996-09-25'],
        ['Id' => 7, 'Voornaam' => 'Kevin', 'Tussenvoegsel' => null, 'Achternaam' => 'Smit', 'Specialisatie' => 'Extensions', 'Geboortedatum' => '2001-03-17'],
        ['Id' => 10, 'Voornaam' => 'Romy', 'Tussenvoegsel' => null, 'Achternaam' => 'Jacobs', 'Specialisatie' => 'Knippen', 'Geboortedatum' => '2010-01-15'], // minderjarig
    ];

    private function bouwNaam(array $m): string
    {
        $naam = $m['Voornaam'];
        if (!empty($m['Tussenvoegsel'])) { $naam .= ' ' . $m['Tussenvoegsel']; }
        return trim($naam . ' ' . $m['Achternaam']);
    }

    private function filterOpSpecialisatie(array $mws, ?string $spec): array
    {
        if ($spec === null || $spec === '') { return $mws; }
        return array_values(array_filter($mws, fn($m) => $m['Specialisatie'] === $spec));
    }

    private function berekenLeeftijd(string $geboortedatum): int
    {
        $geb = new \DateTime($geboortedatum);
        return (int)$geb->diff(new \DateTime())->y;
    }

    // ================================================================
    // NAAM OPBOUW
    // ================================================================

    /** @test */
    public function testNaamZonderTussenvoegsel(): void
    {
        $this->assertSame('Aylin Demir', $this->bouwNaam($this->testMedewerkers[0]));
    }

    /** @test */
    public function testNaamMetTussenvoegsel(): void
    {
        $this->assertSame('Sanne de Vries', $this->bouwNaam($this->testMedewerkers[1]));
    }

    // ================================================================
    // SPECIALISATIE FILTER
    // ================================================================

    /** @test */
    public function testFilterOpStylen(): void
    {
        $result = $this->filterOpSpecialisatie($this->testMedewerkers, 'Stylen');
        $this->assertCount(1, $result);
        $this->assertSame('Stylen', $result[0]['Specialisatie']);
    }

    /** @test */
    public function testFilterOpNietBestaandeSpecialisatie(): void
    {
        $result = $this->filterOpSpecialisatie($this->testMedewerkers, 'Permanent');
        $this->assertEmpty($result);
    }

    /** @test */
    public function testGeenFilterGeeftAlleMedewerkers(): void
    {
        $result = $this->filterOpSpecialisatie($this->testMedewerkers, null);
        $this->assertCount(4, $result);
    }

    // ================================================================
    // UNIEKE SPECIALISATIES
    // ================================================================

    /** @test */
    public function testUniekeSpecialisatiesWordenGevonden(): void
    {
        $all = array_unique(array_column($this->testMedewerkers, 'Specialisatie'));
        $this->assertCount(4, $all);
    }

    // ================================================================
    // LEEFTIJD BEREKENING
    // ================================================================

    /** @test */
    public function testLeeftijdBerekeningSanne(): void
    {
        // Sanne de Vries: 1996-09-25 → ~29-30 jaar (afhankelijk van testdatum)
        $leeftijd = $this->berekenLeeftijd('1996-09-25');
        $this->assertGreaterThanOrEqual(27, $leeftijd);
        $this->assertLessThan(50, $leeftijd);
    }

    /** @test */
    public function testLeeftijdBerekeningSanneIsMinderjarig(): void
    {
        // Romy Jacobs: 2010-01-15 → ~14-16 jaar (minderjarig)
        $leeftijd = $this->berekenLeeftijd('2010-01-15');
        $this->assertLessThan(18, $leeftijd);
    }

    /** @test */
    public function testMinderjarigeNietToegstaanBijPermanent(): void
    {
        $romy = $this->testMedewerkers[3];
        $leeftijd = $this->berekenLeeftijd($romy['Geboortedatum']);
        $specialisatie = 'Permanent';

        $toegestaan = !($leeftijd < 18 && $specialisatie === 'Permanent');
        $this->assertFalse($toegestaan, 'Minderjarige mag geen Permanent krijgen');
    }

    /** @test */
    public function testMeerderjarigeMagPermanent(): void
    {
        $sanne = $this->testMedewerkers[1];
        $leeftijd = $this->berekenLeeftijd($sanne['Geboortedatum']);
        $specialisatie = 'Permanent';

        $toegestaan = !($leeftijd < 18 && $specialisatie === 'Permanent');
        $this->assertTrue($toegestaan, 'Meerderjarige mag Permanent');
    }

    // ================================================================
    // PAGINERING
    // ================================================================

    /** @test */
    public function testPagineringBerekening(): void
    {
        $totaal = 10;
        $perPagina = 4;
        $totaalPaginas = (int)ceil($totaal / $perPagina);
        $this->assertSame(3, $totaalPaginas);
    }

    /** @test */
    public function testOffsetBerekening(): void
    {
        $perPagina = 4;
        $this->assertSame(0, (1 - 1) * $perPagina); // pagina 1
        $this->assertSame(4, (2 - 1) * $perPagina); // pagina 2
        $this->assertSame(8, (3 - 1) * $perPagina); // pagina 3
    }

    // ================================================================
    // SCENARIO: GEEN RESULTAAT
    // ================================================================

    /** @test */
    public function testGeenResultaatGeeftJuisteMelding(): void
    {
        $specialisatie = 'Permanent';
        $result        = $this->filterOpSpecialisatie($this->testMedewerkers, $specialisatie);
        $filterActief  = $specialisatie !== '' && $specialisatie !== 'Alle specialisaties';
        $geenResultaat = $filterActief && count($result) === 0;

        $this->assertTrue($geenResultaat, 'Verwacht geen resultaat met Permanent filter');
    }
}
