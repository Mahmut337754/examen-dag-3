<?php

namespace App\Core;

/**
 * Centrale validatieklasse — gedeeld door alle controllers.
 *
 * Single responsibility: bevat uitsluitend validatieregels.
 */
class Validator
{
    /**
     * Bekende Nederlandse plaatsnamen (uitgebreide lijst).
     * Wordt gebruikt voor validatie van het veld Woonplaats/Plaats.
     */
    private const NL_PLAATSEN = [
        'Aalsmeer','Aalst','Aalten','Abcoude','Achtkarspelen','Alblasserdam','Alkmaar',
        'Almelo','Almere','Alphen aan den Rijn','Alphen-Chaam','Ameland','Amersfoort',
        'Amstelveen','Amsterdam','Andijk','Anna Paulowna','Apeldoorn','Appingedam',
        'Arnhem','Assen','Asten','Baarle-Nassau','Baarn','Barendrecht','Barneveld',
        'Beek','Beemster','Beesel','Bergen','Bergen op Zoom','Berkelland','Bernheze',
        'Best','Beuningen','Beverwijk','Binnenmaas','Bladel','Blaricum','Bloemendaal',
        'Bodegraven','Boekel','Borger-Odoorn','Borne','Borsele','Boxmeer','Boxtel',
        'Breda','Bronckhorst','Brummen','Brunssum','Bunnik','Bunschoten','Buren',
        'Bussum','Capelle aan den IJssel','Castricum','Coevorden','Cranendonck',
        'Cromstrijen','Cuijk','Culemborg','Dalfsen','Dantumadiel','De Bilt',
        'De Friese Meren','De Marne','De Ronde Venen','De Wolden','Delft','Delfzijl',
        'Den Haag','Den Helder','Deurne','Deventer','Diemen','Dinkelland','Doesburg',
        'Doetinchem','Dongen','Dongeradeel','Dordrecht','Drechterland','Drimmelen',
        'Dronten','Druten','Duiven','Echt-Susteren','Edam-Volendam','Ede','Eemnes',
        'Eemsmond','Eersel','Eijsden-Margraten','Eindhoven','Elburg','Emmen',
        'Enkhuizen','Enschede','Epe','Ermelo','Etten-Leur','Ferwerderadiel','Franekeradeel',
        'Geertruidenberg','Geldermalsen','Geldrop-Mierlo','Gemert-Bakel','Gennep',
        'Giessenlanden','Gilze en Rijen','Goeree-Overflakkee','Goes','Goirle',
        'Gorinchem','Gouda','Grave','Groesbeek','Groningen','Grootegast','Gulpen-Wittem',
        'Haaksbergen','Haarlemmermeer','Haarlem','Hardenberg','Harderwijk','Hardinxveld-Giessendam',
        'Harlingen','Hattem','Heemskerk','Heemstede','Heerde','Heerenveen','Heerlen',
        'Heerlijkheid Mariënwaerdt','Heeze-Leende','Heiloo','Hellendoorn','Hellevoetsluis',
        'Helmond','Hendrik-Ido-Ambacht','Hengelo','Hillegom','Hilvarenbeek','Hilversum',
        'Hoeksche Waard','Hof van Twente','Hoogeveen','Hoorn','Horst aan de Maas',
        'Houten','Huizen','Hulst','IJsselstein','Kaag en Braassem','Kampen','Kapelle',
        'Katwijk','Kerkrade','Koggenland','Kollumerland en Nieuwkruisland','Laarbeek',
        'Landerd','Landgraaf','Landsmeer','Langedijk','Laren','Leeuwarden','Leiden',
        'Leiderdorp','Leidschendam-Voorburg','Lelystad','Lemsterland','Leudal','Leusden',
        'Lingewaard','Lisse','Lochem','Loon op Zand','Lopik','Losser','Maasdriel',
        'Maasgouw','Maassluis','Maastricht','Medemblik','Meerssen','Menameradiel',
        'Menterwolde','Meppel','Middelburg','Middelharnis','Midden-Delfland',
        'Midden-Drenthe','Mill en Sint Hubert','Millingen aan de Rijn','Moerdijk',
        'Molenwaard','Montfoort','Mook en Middelaar','Muiden','Naarden','Neder-Betuwe',
        'Needza','Neerijnen','Nieuwegein','Nieuwkoop','Nijkerk','Nijmegen','Noord-Beveland',
        'Noordenveld','Noordoostpolder','Noordwijk','Nuth','Oegstgeest','Oldambt',
        'Oldebroek','Oldenzaal','Olst-Wijhe','Ommen','Oost Gelre','Oosterhout',
        'Ooststellingwerf','Oostzaan','Opmeer','Opsterland','Oss','Oud-Beijerland',
        'Ouder-Amstel','Oudewater','Overbetuwe','Papendrecht','Peel en Maas',
        'Pijnacker-Nootdorp','Purmerend','Putten','Raalte','Reimerswaal','Renkum',
        'Reusel-De Mierden','Rheden','Rhenen','Ridderkerk','Rijnwaarden','Rijnwoude',
        'Rijssen-Holten','Rijswijk','Roerdalen','Roermond','Roosendaal','Rotterdam',
        'Rozendaal','Rucphen','Schagen','Scherpenzeel','Schiedam','Schiermonnikoog',
        'Schouwen-Duiveland','Simpelveld','Sint-Michielsgestel','Sint-Oedenrode',
        'Sittard-Geleen','Sliedrecht','Slochteren','Sluis','Smallingerland','Soest',
        'Someren','Son en Breugel','Spijkenisse','Stadskanaal','Staphorst','Stede Broec',
        'Steenbergen','Steenwijkerland','Stein','Strijen','Sudwest Fryslan','Terneuzen',
        'Terschelling','Texel','Teylingen','Tholen','Tiel','Tilburg','Tubbergen',
        'Twenterand','Tynaarlo','Tytsjerksteradiel','Ubbergen','Uden','Uitgeest',
        'Uithoorn','Urk','Utrecht','Utrechtse Heuvelrug','Vaals','Valkenburg aan de Geul',
        'Valkenswaard','Veendam','Veenendaal','Veere','Veldhoven','Velsen','Venlo',
        'Venray','Vianen','Vlaardingen','Vlagtwedde','Vlissingen','Vlist','Voorne-Putten',
        'Voorschoten','Vught','Waalre','Waalwijk','Waddinxveen','Wageningen','Wassenaar',
        'Waterland','Weert','West Maas en Waal','Westerveld','Westervoort','Westland',
        'Weststellingwerf','Westvoorne','Wierden','Wijchen','Wijdemeren','Wijk bij Duurstede',
        'Winsum','Woensdrecht','Woerden','Wormerland','Woudenberg','Woudrichem','Zaanstad',
        'Zandvoort','Zeewolde','Zeist','Zevenaar','Zijpe','Zoetermeer','Zoeterwoude',
        'Zuidhorn','Zuidplas','Zundert','Zutphen','Zwartewaterland','Zwijndrecht','Zwolle',
        // Relevante plaatsen uit de testdata
        'Utrecht','Amsterdam','Rotterdam','Den Haag','Eindhoven','Groningen','Tilburg',
        'Almere','Breda','Nijmegen','Enschede','Haarlem','Arnhem','Zaandam','Haarlemmermeer',
        'Amersfoort','Apeldoorn','\'s-Hertogenbosch','Hoofddorp','Maastricht',
    ];

    // ----------------------------------------------------------------
    // Huisnummer
    // ----------------------------------------------------------------

    /**
     * Valideert een huisnummer: moet een positief geheel getal zijn (>= 1).
     * Geen negatieve getallen, geen 0, geen letters alleen.
     */
    public static function isGeldigHuisnummer(string $huisnummer): bool
    {
        // Verwijder eventuele spaties
        $h = trim($huisnummer);

        // Moet beginnen met een positief getal (1 of meer)
        // Mag optioneel een toevoeging hebben zoals "4a" of "12B", maar het getal moet >= 1
        if (!preg_match('/^([1-9]\d*)([a-zA-Z\-\s]*)$/', $h, $matches)) {
            return false;
        }

        $numeriek = (int) $matches[1];
        return $numeriek >= 1 && $numeriek <= 99999;
    }

    /**
     * Geeft foutmelding terug als huisnummer ongeldig is, anders null.
     */
    public static function foutHuisnummer(string $huisnummer): ?string
    {
        if (trim($huisnummer) === '') {
            return 'Huisnummer is verplicht';
        }
        if (!self::isGeldigHuisnummer($huisnummer)) {
            return 'Huisnummer moet een positief getal zijn (minimaal 1, geen negatieve getallen)';
        }
        return null;
    }

    // ----------------------------------------------------------------
    // Woonplaats / Plaats
    // ----------------------------------------------------------------

    /**
     * Valideert of een plaatsnaam bestaat in Nederland.
     * Case-insensitief.
     */
    public static function isGeldigePlaats(string $plaats): bool
    {
        $p = trim($plaats);
        if (empty($p)) {
            return false;
        }

        // Case-insensitief vergelijken
        foreach (self::NL_PLAATSEN as $bekendePlaats) {
            if (mb_strtolower($p, 'UTF-8') === mb_strtolower($bekendePlaats, 'UTF-8')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Geeft foutmelding terug als plaatsnaam ongeldig is, anders null.
     */
    public static function foutPlaats(string $plaats): ?string
    {
        if (empty(trim($plaats))) {
            return 'Woonplaats is verplicht';
        }
        if (!self::isGeldigePlaats($plaats)) {
            return 'Voer een geldige Nederlandse woonplaats in (bijv. Utrecht, Amsterdam)';
        }
        return null;
    }

    // ----------------------------------------------------------------
    // E-mail
    // ----------------------------------------------------------------

    public static function isGeldigEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function foutEmail(string $email, string $label = 'E-mailadres'): ?string
    {
        if (empty(trim($email))) {
            return "{$label} is verplicht";
        }
        if (!self::isGeldigEmail($email)) {
            return "Voer een geldig e-mailadres in";
        }
        return null;
    }

    // ----------------------------------------------------------------
    // Postcode
    // ----------------------------------------------------------------

    public static function isGeldigePostcode(string $postcode): bool
    {
        return (bool) preg_match('/^\d{4}\s?[A-Za-z]{2}$/', trim($postcode));
    }

    public static function foutPostcode(string $postcode): ?string
    {
        if (empty(trim($postcode))) {
            return 'Postcode is verplicht';
        }
        if (!self::isGeldigePostcode($postcode)) {
            return 'Voer een geldige postcode in (bijv. 3512AB)';
        }
        return null;
    }

    // ----------------------------------------------------------------
    // Telefoonnummer / Mobiel
    // ----------------------------------------------------------------

    /**
     * Valideert een Nederlands telefoonnummer.
     * Accepteert formaten zoals: 0612345678, +31612345678, 06-12345678, etc.
     */
    public static function isGeldigTelefoonnummer(string $telefoon): bool
    {
        // Verwijder spaties, streepjes, haakjes en punten voor validatie
        $gezuiverd = preg_replace('/[\s\-\(\)\.]/', '', $telefoon);
        
        // Moet beginnen met 0 of +31 (Nederlandse landcode)
        // Moet 9-10 cijfers bevatten (exclusief landcode)
        return (bool) preg_match('/^(\+31|0)[0-9]{9}$/', $gezuiverd);
    }

    /**
     * Geeft foutmelding terug als telefoonnummer ongeldig is, anders null.
     */
    public static function foutTelefoonnummer(string $telefoon, string $label = 'Telefoonnummer'): ?string
    {
        if (empty(trim($telefoon))) {
            return "{$label} is verplicht";
        }
        if (!self::isGeldigTelefoonnummer($telefoon)) {
            return "Voer een geldig telefoonnummer in (bijv. 0612345678 of +31612345678)";
        }
        return null;
    }

    // ----------------------------------------------------------------
    // Verplicht tekstveld
    // ----------------------------------------------------------------

    public static function foutVerplicht(string $waarde, string $label): ?string
    {
        return empty(trim($waarde)) ? "{$label} is verplicht" : null;
    }

    // ----------------------------------------------------------------
    // Naam validatie (geen nummers toegestaan)
    // ----------------------------------------------------------------

    /**
     * Controleert of een naam geen nummers bevat.
     */
    public static function isGeldigeNaam(string $naam): bool
    {
        return !preg_match('/[0-9]/', $naam);
    }

    /**
     * Geeft foutmelding terug als naam nummers bevat, anders null.
     */
    public static function foutNaam(string $naam, string $label = 'Naam'): ?string
    {
        if (empty(trim($naam))) {
            return "{$label} is verplicht";
        }
        if (!self::isGeldigeNaam($naam)) {
            return "{$label} mag geen nummers bevatten";
        }
        return null;
    }
}
