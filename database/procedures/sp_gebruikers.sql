-- =====================================================
-- Stored procedures: gebruikers
-- Importeer via phpMyAdmin: zonder DELIMITER-syntax
-- =====================================================
USE `kniploket_tiko`;

-- ---------------------------------------------------
-- SP: Haal gebruiker op via e-mail (voor inloggen)
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_gebruiker_ophalen_email`;

CREATE PROCEDURE `sp_gebruiker_ophalen_email`(
    IN p_email VARCHAR(255)
)
BEGIN
    SELECT
        g.id,
        g.naam,
        g.email,
        g.wachtwoord,
        g.rol_id,
        g.is_actief,
        r.naam AS rol_naam
    FROM `gebruikers` g
    INNER JOIN `rollen` r ON r.id = g.rol_id
    WHERE g.email = p_email
    LIMIT 1;
END;

-- ---------------------------------------------------
-- SP: Haal gebruiker op via ID
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_gebruiker_ophalen_id`;

CREATE PROCEDURE `sp_gebruiker_ophalen_id`(
    IN p_id INT UNSIGNED
)
BEGIN
    SELECT
        g.id,
        g.naam,
        g.email,
        g.wachtwoord,
        g.rol_id,
        g.is_actief,
        r.naam AS rol_naam
    FROM `gebruikers` g
    INNER JOIN `rollen` r ON r.id = g.rol_id
    WHERE g.id = p_id
    LIMIT 1;
END;

-- ---------------------------------------------------
-- SP: Wijzig wachtwoord van gebruiker
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_gebruiker_wachtwoord_wijzigen`;

CREATE PROCEDURE `sp_gebruiker_wachtwoord_wijzigen`(
    IN p_id         INT UNSIGNED,
    IN p_wachtwoord VARCHAR(255)
)
BEGIN
    UPDATE `gebruikers`
    SET `wachtwoord` = p_wachtwoord
    WHERE `id` = p_id;
END;

-- ---------------------------------------------------
-- SP: Controleer of e-mail al bestaat (excl. eigen id)
-- ---------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_gebruiker_email_bestaat`;

CREATE PROCEDURE `sp_gebruiker_email_bestaat`(
    IN p_email VARCHAR(255),
    IN p_id    INT UNSIGNED
)
BEGIN
    SELECT COUNT(*) AS `aantal`
    FROM `gebruikers`
    WHERE `email` = p_email
      AND `id` != p_id;
END;
