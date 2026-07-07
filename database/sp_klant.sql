-- ============================================================
-- Kniploket Tiko - Stored Procedures voor Klant
-- ============================================================

USE kniploket_tiko;

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
-- Validatie: Email uniciteit check
-- Parameters: alle klant- en contactvelden + OUT success/message
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_UpdateKlantGegevens;
DELIMITER $$
CREATE PROCEDURE sp_UpdateKlantGegevens(
    IN p_klant_id INT,
    IN p_voornaam VARCHAR(100),
    IN p_tussenvoegsel VARCHAR(50),
    IN p_achternaam VARCHAR(100),
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
        -- Update Klant gegevens
            UPDATE Klant 
            SET Voornaam      = p_voornaam,
                Tussenvoegsel = p_tussenvoegsel,
                Achternaam    = p_achternaam,
                Bijzonderheden = p_bijzonderheden,
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
