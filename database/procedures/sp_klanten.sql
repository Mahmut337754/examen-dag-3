-- =====================================================
-- Stored procedures: klanten
-- Importeer via phpMyAdmin: zonder DELIMITER-syntax
-- =====================================================
USE `kniploket_tiko1`;

-- ---------------------------------------------------
-- SP: Overzicht alle klanten (JOIN gebruikers)
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_klanten_overzicht`;

CREATE PROCEDURE `sp_klanten_overzicht`()
BEGIN
    SELECT
        k.id,
        g.naam,
        g.email,
        k.telefoonnummer,
        k.adres,
        k.allergieen,
        k.wensen,
        g.is_actief,
        g.aangemaakt_op
    FROM `klanten` k
    INNER JOIN `gebruikers` g ON g.id = k.gebruiker_id
    ORDER BY g.naam ASC;
END;

-- ---------------------------------------------------
-- SP: Detail van één klant op basis van klant-id
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_klant_detail`;

CREATE PROCEDURE `sp_klant_detail`(
    IN p_klant_id INT UNSIGNED
)
BEGIN
    SELECT
        k.id,
        k.gebruiker_id,
        g.naam,
        g.email,
        k.telefoonnummer,
        k.adres,
        k.allergieen,
        k.wensen,
        g.is_actief
    FROM `klanten` k
    INNER JOIN `gebruikers` g ON g.id = k.gebruiker_id
    WHERE k.id = p_klant_id
    LIMIT 1;
END;

-- ---------------------------------------------------
-- SP: Klant toevoegen (gebruiker + klant in transactie)
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_klant_toevoegen`;

CREATE PROCEDURE `sp_klant_toevoegen`(
    IN  p_naam           VARCHAR(100),
    IN  p_email          VARCHAR(255),
    IN  p_wachtwoord     VARCHAR(255),
    IN  p_adres          VARCHAR(255),
    IN  p_telefoonnummer VARCHAR(20),
    IN  p_allergieen     TEXT,
    IN  p_wensen         TEXT,
    OUT p_nieuw_id       INT UNSIGNED,
    OUT p_fout           VARCHAR(255)
)
BEGIN
    DECLARE v_gebruiker_id INT UNSIGNED DEFAULT 0;
    DECLARE v_email_count  INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_nieuw_id = 0;
        SET p_fout = 'Databasefout opgetreden bij aanmaken klant.';
    END;

    SELECT COUNT(*) INTO v_email_count
    FROM `gebruikers`
    WHERE `email` = p_email;

    IF v_email_count > 0 THEN
        SET p_nieuw_id = 0;
        SET p_fout = 'E-mailadres is al in gebruik.';
    ELSE
        START TRANSACTION;

        INSERT INTO `gebruikers` (`naam`, `email`, `wachtwoord`, `rol_id`)
        VALUES (p_naam, p_email, p_wachtwoord, 3);

        SET v_gebruiker_id = LAST_INSERT_ID();

        INSERT INTO `klanten` (`gebruiker_id`, `adres`, `telefoonnummer`, `allergieen`, `wensen`)
        VALUES (v_gebruiker_id, p_adres, p_telefoonnummer, p_allergieen, p_wensen);

        SET p_nieuw_id = LAST_INSERT_ID();
        SET p_fout = '';

        COMMIT;
    END IF;
END;

-- ---------------------------------------------------
-- SP: Klant wijzigen (gebruiker + klant in transactie)
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_klant_wijzigen`;

CREATE PROCEDURE `sp_klant_wijzigen`(
    IN  p_klant_id       INT UNSIGNED,
    IN  p_naam           VARCHAR(100),
    IN  p_email          VARCHAR(255),
    IN  p_wachtwoord     VARCHAR(255),
    IN  p_adres          VARCHAR(255),
    IN  p_telefoonnummer VARCHAR(20),
    IN  p_allergieen     TEXT,
    IN  p_wensen         TEXT,
    OUT p_fout           VARCHAR(255)
)
BEGIN
    DECLARE v_gebruiker_id INT UNSIGNED DEFAULT 0;
    DECLARE v_email_count  INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_fout = 'Databasefout opgetreden bij wijzigen klant.';
    END;

    SELECT `gebruiker_id` INTO v_gebruiker_id
    FROM `klanten`
    WHERE `id` = p_klant_id
    LIMIT 1;

    IF v_gebruiker_id = 0 THEN
        SET p_fout = 'Klant niet gevonden.';
    ELSE
        SELECT COUNT(*) INTO v_email_count
        FROM `gebruikers`
        WHERE `email` = p_email
          AND `id` != v_gebruiker_id;

        IF v_email_count > 0 THEN
            SET p_fout = 'E-mailadres is al in gebruik door een andere gebruiker.';
        ELSE
            START TRANSACTION;

            UPDATE `gebruikers`
            SET `naam`  = p_naam,
                `email` = p_email
            WHERE `id` = v_gebruiker_id;

            IF p_wachtwoord != '' THEN
                UPDATE `gebruikers`
                SET `wachtwoord` = p_wachtwoord
                WHERE `id` = v_gebruiker_id;
            END IF;

            UPDATE `klanten`
            SET `adres`          = p_adres,
                `telefoonnummer` = p_telefoonnummer,
                `allergieen`     = p_allergieen,
                `wensen`         = p_wensen
            WHERE `id` = p_klant_id;

            SET p_fout = '';
            COMMIT;
        END IF;
    END IF;
END;

-- ---------------------------------------------------
-- SP: Klant verwijderen
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_klant_verwijderen`;

CREATE PROCEDURE `sp_klant_verwijderen`(
    IN  p_klant_id INT UNSIGNED,
    OUT p_fout     VARCHAR(255)
)
BEGIN
    DECLARE v_gebruiker_id INT UNSIGNED DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_fout = 'Databasefout opgetreden bij verwijderen klant.';
    END;

    SELECT `gebruiker_id` INTO v_gebruiker_id
    FROM `klanten`
    WHERE `id` = p_klant_id
    LIMIT 1;

    IF v_gebruiker_id = 0 THEN
        SET p_fout = 'Klant niet gevonden.';
    ELSE
        START TRANSACTION;
        DELETE FROM `gebruikers` WHERE `id` = v_gebruiker_id;
        SET p_fout = '';
        COMMIT;
    END IF;
END;

-- ---------------------------------------------------
-- SP: Statistieken dashboard
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_dashboard_statistieken`;

CREATE PROCEDURE `sp_dashboard_statistieken`()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM `klanten`)                               AS aantal_klanten,
        (SELECT COUNT(*) FROM `afspraken` WHERE `status` = 'gepland') AS geplande_afspraken,
        (SELECT COUNT(*) FROM `medewerkers`)                          AS aantal_medewerkers,
        (SELECT COUNT(*) FROM `producten` WHERE `voorraad` = 0)       AS producten_uitverkocht;
END;
