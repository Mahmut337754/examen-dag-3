# Test Suite

Deze directory bevat unit tests voor de Product en Klant modellen.

## Bestanden

- `ProductTest.php` - PHPUnit tests voor Product model
- `KlantTest.php` - PHPUnit tests voor Klant model
- `run_tests.php` - Simple test runner (zonder PHPUnit)

## Uitvoeren van tests

### Optie 1: Met PHPUnit (aanbevolen)

```bash
# Installeer dependencies
composer install

# Voer alle tests uit
vendor/bin/phpunit tests/ProductTest.php tests/KlantTest.php

# Of voer individuele test bestanden uit
vendor/bin/phpunit tests/ProductTest.php
vendor/bin/phpunit tests/KlantTest.php
```

### Optie 2: Met de simple test runner

```bash
# Voer tests uit zonder PHPUnit
php tests/run_tests.php
```

## Test Coverage

### ProductTest.php
- ✓ testAlleLeveranciers - Test ophalen van alle leveranciers
- ✓ testVindOpId - Test product opzoeken op ID
- ✓ testAanmaken - Test product aanmaken
- ✓ testDubbeleProductnaam - Test dubbele productnaam validatie
- ✓ testDubbeleEanCode - Test dubbele EAN-code validatie
- ✓ testWijzigen - Test product wijzigen
- ✓ testVerwijderen - Test product verwijderen
- ✓ testStatistieken - Test statistieken ophalen

### KlantTest.php
- ✓ testAlleAllergenen - Test ophalen van alle allergenen
- ✓ testOverzicht - Test ophalen van alle klanten
- ✓ testVindOpId - Test klant opzoeken op ID
- ✓ testAanmaken - Test klant aanmaken
- ✓ testDubbeleEmail - Test dubbele e-mail validatie
- ✓ testWijzigen - Test klant wijzigen
- ✓ testVerwijderen - Test klant verwijderen
- ✓ testStatistieken - Test statistieken ophalen
- ✓ testAllergenenVanKlant - Test allergenen van klant ophalen

## Vereisten

- PHP 8.1 of hoger
- MySQL database (kniploket_tiko)
- Composer (voor PHPUnit)

## Database

Tests maken gebruik van de database en voeren daadwerkelijke CRUD operaties uit.
Elke test maakt testdata aan en ruimt deze daarna op.