-- ============================================================
-- Kniploket Tiko - Stored Procedures voor Medewerker
-- ============================================================

USE kniploket_tiko;

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

-- ------------------------------------------------------------
-- Stored Procedure: sp_UpdateMedewerkerGegevens
-- Beschrijving: Wijzigt medewerkergegevens en bijbehorende contactgegevens
-- Validatie: minderjarige (<18) mag geen specialisatie 'Permanent' krijgen
-- Parameters: alle medewerker- en contactvelden + OUT success/message
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_UpdateMedewerkerGegevens;
DELIMITER $$
CREATE PROCEDURE sp_UpdateMedewerkerGegevens(
    IN  p_medewerker_id  INT,
    IN  p_specialisatie  VARCHAR(100),
    IN  p_geboortedatum  DATE,
    IN  p_contact_email  VARCHAR(255),
    IN  p_straatnaam     VARCHAR(255),
    IN  p_huisnummer     VARCHAR(20),
    IN  p_toevoeging     VARCHAR(20),
    IN  p_postcode       VARCHAR(10),
    IN  p_plaats         VARCHAR(100),
    IN  p_mobiel         VARCHAR(20),
    IN  p_opmerking      VARCHAR(255),
    OUT p_success        BOOLEAN,
    OUT p_message        VARCHAR(500)
)
BEGIN
    DECLARE v_contact_id INT;
    DECLARE v_leeftijd   INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_success = FALSE;
        SET p_message = 'Database fout bij het wijzigen van medewerkergegevens';
        ROLLBACK;
    END;

    START TRANSACTION;

    -- Bereken leeftijd op basis van geboortedatum
    SET v_leeftijd = TIMESTAMPDIFF(YEAR, p_geboortedatum, CURDATE());

    -- Validatie: minderjarige mag geen specialisatie 'Permanent' krijgen
    IF v_leeftijd < 18 AND p_specialisatie = 'Permanent' THEN
        SET p_success = FALSE;
        SET p_message = 'Minderjarige medewerkers mogen geen specialisatie Permanent toegewezen krijgen vanwege het werken met gevaarlijke stoffen en chemicaliën.';
        ROLLBACK;
    ELSE
        -- Haal ContactId op voor deze medewerker
        SELECT c.Id INTO v_contact_id
        FROM Contact c
        INNER JOIN MedewerkerPerContact mpc ON mpc.ContactId = c.Id
        WHERE mpc.MedewerkerId = p_medewerker_id AND mpc.IsActief = 1 AND c.IsActief = 1
        LIMIT 1;

        -- Update Medewerker
        UPDATE Medewerker
        SET Specialisatie  = p_specialisatie,
            Geboortedatum  = p_geboortedatum,
            Opmerking      = p_opmerking,
            DatumGewijzigd = CURRENT_TIMESTAMP(6)
        WHERE Id = p_medewerker_id;

        -- Update Contact indien gevonden
        IF v_contact_id IS NOT NULL THEN
            UPDATE Contact
            SET Email          = p_contact_email,
                Straatnaam     = p_straatnaam,
                Huisnummer     = p_huisnummer,
                Toevoeging     = p_toevoeging,
                Postcode       = p_postcode,
                Plaats         = p_plaats,
                Mobiel         = p_mobiel,
                DatumGewijzigd = CURRENT_TIMESTAMP(6)
            WHERE Id = v_contact_id;
        END IF;

        SET p_success = TRUE;
        SET p_message = 'Medewerkergegevens succesvol bijgewerkt';
        COMMIT;
    END IF;
END$$
DELIMITER ;
