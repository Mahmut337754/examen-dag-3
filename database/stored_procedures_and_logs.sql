-- ============================================================
-- Kniploket Tiko - Stored Procedures en Technische Logs
-- Uitvoeren na het draaien van database.sql
-- ============================================================

USE kniploket_tiko;

-- ============================================================
-- STORED PROCEDURES
-- ============================================================

-- ------------------------------------------------------------
-- Stored Procedure: sp_GetKlantenMetContactGegevens
-- Beschrijving: Haalt klanten op met contactgegevens, optioneel gefilterd op postcode
-- Parameters: 
--   - p_postcode: VARCHAR(10) - Optionele postcode filter (NULL voor alle klanten)
--   - p_limit: INT - Aantal records per pagina
--   - p_offset: INT - Offset voor paginering
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_GetKlantenMetContactGegevens;
DELIMITER $$
CREATE PROCEDURE sp_GetKlantenMetContactGegevens(
    IN p_postcode VARCHAR(10),
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    SELECT 
        k.Id,
        k.Voornaam,
        k.Tussenvoegsel,
        k.Achternaam,
        k.Relatienummer,
        k.Bijzonderheden,
        c.Straatnaam,
        c.Huisnummer,
        c.Toevoeging,
        c.Postcode,
        c.Plaats,
        c.Email AS ContactEmail,
        c.Mobiel,
        u.email AS AccountEmail,
        k.DatumAangemaakt,
        k.DatumGewijzigd
    FROM Klant k
    INNER JOIN users u ON u.id = k.UserId
    LEFT JOIN KlantPerContact kpc ON kpc.KlantId = k.Id AND kpc.IsActief = 1
    LEFT JOIN Contact c ON c.Id = kpc.ContactId AND c.IsActief = 1
    WHERE k.IsActief = 1
        AND (p_postcode IS NULL OR REPLACE(c.Postcode, ' ', '') LIKE CONCAT(REPLACE(p_postcode, ' ', ''), '%'))
    ORDER BY k.Achternaam, k.Voornaam
    LIMIT p_limit OFFSET p_offset;
END$$
DELIMITER ;

-- ------------------------------------------------------------
-- Stored Procedure: sp_CountKlantenMetContactGegevens
-- Beschrijving: Telt het totaal aantal klanten, optioneel gefilterd op postcode
-- Parameters: 
--   - p_postcode: VARCHAR(10) - Optionele postcode filter
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_CountKlantenMetContactGegevens;
DELIMITER $$
CREATE PROCEDURE sp_CountKlantenMetContactGegevens(
    IN p_postcode VARCHAR(10)
)
BEGIN
    SELECT COUNT(DISTINCT k.Id) AS TotaalKlanten
    FROM Klant k
    LEFT JOIN KlantPerContact kpc ON kpc.KlantId = k.Id AND kpc.IsActief = 1
    LEFT JOIN Contact c ON c.Id = kpc.ContactId AND c.IsActief = 1
    WHERE k.IsActief = 1
        AND (p_postcode IS NULL OR REPLACE(c.Postcode, ' ', '') LIKE CONCAT(REPLACE(p_postcode, ' ', ''), '%'));
END$$
DELIMITER ;

-- ------------------------------------------------------------
-- Stored Procedure: sp_UpdateKlantGegevens
-- Beschrijving: Wijzigt klantgegevens en bijbehorende contactgegevens
-- Parameters: alle klant- en contactvelden
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_UpdateKlantGegevens;
DELIMITER $$
CREATE PROCEDURE sp_UpdateKlantGegevens(
    IN p_klant_id INT,
    IN p_contact_email VARCHAR(255),
    IN p_straatnaam VARCHAR(255),
    IN p_huisnummer VARCHAR(20),
    IN p_toevoeging VARCHAR(20),
    IN p_postcode VARCHAR(10),
    IN p_plaats VARCHAR(100),
    IN p_mobiel VARCHAR(20),
    IN p_bijzonderheden TEXT,
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_contact_id INT;
    DECLARE v_email_exists INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_success = FALSE;
        SET p_message = 'Database fout bij het wijzigen van klantgegevens';
        ROLLBACK;
    END;

    START TRANSACTION;

    -- Haal ContactId op voor deze klant
    SELECT c.Id INTO v_contact_id
    FROM Contact c
    INNER JOIN KlantPerContact kpc ON kpc.ContactId = c.Id
    WHERE kpc.KlantId = p_klant_id AND kpc.IsActief = 1 AND c.IsActief = 1
    LIMIT 1;

    IF v_contact_id IS NULL THEN
        SET p_success = FALSE;
        SET p_message = 'Contactgegevens niet gevonden voor deze klant';
        ROLLBACK;
    ELSE
        -- Check of email al bestaat bij een ander contact
        SELECT COUNT(*) INTO v_email_exists
        FROM Contact
        WHERE Email = p_contact_email AND Id != v_contact_id AND IsActief = 1;

        IF v_email_exists > 0 THEN
            SET p_success = FALSE;
            SET p_message = 'Het e-mailadres is al in gebruik';
            ROLLBACK;
        ELSE
            -- Update Klant bijzonderheden
            UPDATE Klant 
            SET Bijzonderheden = p_bijzonderheden,
                DatumGewijzigd = CURRENT_TIMESTAMP(6)
            WHERE Id = p_klant_id;

            -- Update Contact gegevens
            UPDATE Contact
            SET Email = p_contact_email,
                Straatnaam = p_straatnaam,
                Huisnummer = p_huisnummer,
                Toevoeging = p_toevoeging,
                Postcode = p_postcode,
                Plaats = p_plaats,
                Mobiel = p_mobiel,
                DatumGewijzigd = CURRENT_TIMESTAMP(6)
            WHERE Id = v_contact_id;

            SET p_success = TRUE;
            SET p_message = 'Klantgegevens succesvol bijgewerkt';
            COMMIT;
        END IF;
    END IF;
END$$
DELIMITER ;

-- ------------------------------------------------------------
-- Stored Procedure: sp_GetMedewerkersMetContactGegevens
-- Beschrijving: Haalt medewerkers op met contactgegevens, optioneel gefilterd op specialisatie
-- Parameters: 
--   - p_specialisatie: VARCHAR(100) - Optionele specialisatie filter (NULL voor alle medewerkers)
--   - p_limit: INT - Aantal records per pagina
--   - p_offset: INT - Offset voor paginering
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_GetMedewerkersMetContactGegevens;
DELIMITER $$
CREATE PROCEDURE sp_GetMedewerkersMetContactGegevens(
    IN p_specialisatie VARCHAR(100),
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    SELECT 
        m.Id,
        m.Voornaam,
        m.Tussenvoegsel,
        m.Achternaam,
        m.Specialisatie,
        m.Geboortedatum,
        c.Straatnaam,
        c.Huisnummer,
        c.Toevoeging,
        c.Postcode,
        c.Plaats,
        c.Email AS ContactEmail,
        c.Mobiel,
        u.email AS AccountEmail,
        m.DatumAangemaakt,
        m.DatumGewijzigd
    FROM Medewerker m
    INNER JOIN users u ON u.id = m.UserId
    LEFT JOIN MedewerkerPerContact mpc ON mpc.MedewerkerId = m.Id AND mpc.IsActief = 1
    LEFT JOIN Contact c ON c.Id = mpc.ContactId AND c.IsActief = 1
    WHERE m.IsActief = 1
        AND (p_specialisatie IS NULL OR p_specialisatie = '' OR m.Specialisatie = p_specialisatie)
    ORDER BY m.Achternaam, m.Voornaam
    LIMIT p_limit OFFSET p_offset;
END$$
DELIMITER ;

-- ------------------------------------------------------------
-- Stored Procedure: sp_CountMedewerkersMetContactGegevens
-- Beschrijving: Telt het totaal aantal medewerkers, optioneel gefilterd op specialisatie
-- Parameters: 
--   - p_specialisatie: VARCHAR(100) - Optionele specialisatie filter
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_CountMedewerkersMetContactGegevens;
DELIMITER $$
CREATE PROCEDURE sp_CountMedewerkersMetContactGegevens(
    IN p_specialisatie VARCHAR(100)
)
BEGIN
    SELECT COUNT(DISTINCT m.Id) AS TotaalMedewerkers
    FROM Medewerker m
    WHERE m.IsActief = 1
        AND (p_specialisatie IS NULL OR p_specialisatie = '' OR m.Specialisatie = p_specialisatie);
END$$
DELIMITER ;

-- ------------------------------------------------------------
-- Stored Procedure: sp_GetUniekeSpecialisaties
-- Beschrijving: Haalt alle unieke specialisaties op van actieve medewerkers
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_GetUniekeSpecialisaties;
DELIMITER $$
CREATE PROCEDURE sp_GetUniekeSpecialisaties()
BEGIN
    SELECT DISTINCT Specialisatie
    FROM Medewerker
    WHERE IsActief = 1
    ORDER BY Specialisatie;
END$$
DELIMITER ;

-- ============================================================
-- TECHNICAL LOG TABLE
-- ============================================================

CREATE TABLE IF NOT EXISTS TechnischeLog (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    LogType ENUM('INFO', 'WARNING', 'ERROR', 'DEBUG') NOT NULL DEFAULT 'INFO',
    Module VARCHAR(100) NOT NULL COMMENT 'Naam van de module/class/functie',
    Actie VARCHAR(255) NOT NULL COMMENT 'Beschrijving van de actie',
    Details TEXT NULL COMMENT 'Gedetailleerde informatie, bijv. JSON data',
    UserId INT NULL COMMENT 'Optioneel: welke gebruiker voerde de actie uit',
    IpAdres VARCHAR(45) NULL COMMENT 'IP-adres van de gebruiker',
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    INDEX idx_logtype (LogType),
    INDEX idx_module (Module),
    INDEX idx_datum (DatumAangemaakt),
    INDEX idx_userid (UserId),
    CONSTRAINT fk_technischelog_user FOREIGN KEY (UserId) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Technische logs voor debugging en audit trail';

-- ============================================================
-- Insert sample technical log entries
-- ============================================================
INSERT INTO TechnischeLog (LogType, Module, Actie, Details, UserId, IpAdres) VALUES
('INFO', 'KlantController', 'Klant overzicht bekeken', '{"postcode":"3512AB","pagina":1}', 1, '127.0.0.1'),
('INFO', 'KlantController', 'Klant gewijzigd', '{"klant_id":4,"velden":["contact_email","mobiel"]}', 1, '127.0.0.1'),
('WARNING', 'KlantController', 'Poging tot wijzigen met bestaand e-mailadres', '{"klant_id":4,"email":"jan.jansen@outlook.com"}', 1, '127.0.0.1'),
('INFO', 'MedewerkerController', 'Medewerkers overzicht bekeken', '{"specialisatie":"Knippen","pagina":1}', 1, '127.0.0.1'),
('INFO', 'AuthController', 'Gebruiker ingelogd', '{"email":"lisa@kniploket.nl","rol":"eigenaar"}', 1, '127.0.0.1'),
('ERROR', 'Database', 'Connectie mislukt', '{"error":"Connection refused","host":"127.0.0.1"}', NULL, '127.0.0.1');

-- ============================================================
-- VERIFICATIE QUERIES
-- ============================================================

-- Controleer of stored procedures zijn aangemaakt
SHOW PROCEDURE STATUS WHERE Db = 'kniploket_tiko';

-- Controleer of TechnischeLog tabel bestaat
SHOW TABLES LIKE 'TechnischeLog';

-- Toon sample logs
SELECT * FROM TechnischeLog ORDER BY DatumAangemaakt DESC LIMIT 10;
