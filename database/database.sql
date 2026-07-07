-- =====================================================
-- Database: kniploket_tiko1
-- =====================================================

-- Drop en hermaak de database zodat het script meerdere keren uitvoerbaar is
DROP DATABASE IF EXISTS `kniploket_tiko1`;

CREATE DATABASE `kniploket_tiko1`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `kniploket_tiko1`;

-- -------------------------------------------------------
-- Tabel: rollen
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rollen` (
    `id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `naam` VARCHAR(50)  NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_naam` (`naam`)
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: gebruikers
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `gebruikers` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `naam`          VARCHAR(100)  NOT NULL,
    `email`         VARCHAR(255)  NOT NULL,
    `wachtwoord`    VARCHAR(255)  NOT NULL COMMENT 'bcrypt hash',
    `rol_id`        INT UNSIGNED  NOT NULL,
    `is_actief`     TINYINT(1)    NOT NULL DEFAULT 1,
    `aangemaakt_op` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `gewijzigd_op`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_email` (`email`),
    KEY `fk_gebruikers_rol` (`rol_id`),
    CONSTRAINT `fk_gebruikers_rol`
        FOREIGN KEY (`rol_id`)
        REFERENCES `rollen` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: klanten
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `klanten` (
    `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `gebruiker_id`   INT UNSIGNED  NOT NULL,
    `adres`          VARCHAR(255)  DEFAULT NULL,
    `telefoonnummer` VARCHAR(20)   DEFAULT NULL,
    `allergieen`     TEXT          DEFAULT NULL,
    `wensen`         TEXT          DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_gebruiker_id` (`gebruiker_id`),
    CONSTRAINT `fk_klanten_gebruiker`
        FOREIGN KEY (`gebruiker_id`)
        REFERENCES `gebruikers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: allergenen
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `allergenen` (
    `id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `naam` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_naam` (`naam`)
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: klant_allergenen (koppeltabel)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `klant_allergenen` (
    `klant_id`     INT UNSIGNED NOT NULL,
    `allergeen_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`klant_id`, `allergeen_id`),
    CONSTRAINT `fk_ka_klant`
        FOREIGN KEY (`klant_id`)
        REFERENCES `klanten` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_ka_allergeen`
        FOREIGN KEY (`allergeen_id`)
        REFERENCES `allergenen` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: medewerkers
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `medewerkers` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `gebruiker_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_gebruiker_id` (`gebruiker_id`),
    CONSTRAINT `fk_medewerkers_gebruiker`
        FOREIGN KEY (`gebruiker_id`)
        REFERENCES `gebruikers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: specialisaties
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `specialisaties` (
    `id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `naam` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_naam` (`naam`)
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: medewerker_specialisatie
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `medewerker_specialisatie` (
    `medewerker_id`    INT UNSIGNED NOT NULL,
    `specialisatie_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`medewerker_id`, `specialisatie_id`),
    CONSTRAINT `fk_ms_medewerker`
        FOREIGN KEY (`medewerker_id`)
        REFERENCES `medewerkers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_ms_specialisatie`
        FOREIGN KEY (`specialisatie_id`)
        REFERENCES `specialisaties` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: werktijden
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `werktijden` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `medewerker_id` INT UNSIGNED  NOT NULL,
    `dag_van_week`  TINYINT UNSIGNED NOT NULL COMMENT '1=maandag ... 7=zondag',
    `starttijd`     TIME          NOT NULL,
    `eindtijd`      TIME          NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_medewerker_dag` (`medewerker_id`, `dag_van_week`),
    CONSTRAINT `fk_werktijden_medewerker`
        FOREIGN KEY (`medewerker_id`)
        REFERENCES `medewerkers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: leveranciers
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `leveranciers` (
    `id`   INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `naam` VARCHAR(150)  NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_naam` (`naam`)
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: behandelingen
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `behandelingen` (
    `id`            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `naam`          VARCHAR(150)   NOT NULL,
    `prijs`         DECIMAL(8,2)   NOT NULL,
    `duur_minuten`  INT UNSIGNED   NOT NULL,
    `beschrijving`  TEXT           DEFAULT NULL,
    `aangemaakt_op` DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `gewijzigd_op`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_naam` (`naam`)
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: producten
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `producten` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `productnaam`   VARCHAR(150)  NOT NULL,
    `categorie`     VARCHAR(100)  NOT NULL,
    `ean_code`      VARCHAR(13)   NOT NULL,
    `voorraad`      INT UNSIGNED  NOT NULL DEFAULT 0,
    `leverancier_id` INT UNSIGNED NOT NULL,
    `prijs`         DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
    `aangemaakt_op` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `gewijzigd_op`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_productnaam` (`productnaam`),
    UNIQUE KEY `uk_ean_code` (`ean_code`),
    KEY `fk_producten_leverancier` (`leverancier_id`),
    CONSTRAINT `fk_producten_leverancier`
        FOREIGN KEY (`leverancier_id`)
        REFERENCES `leveranciers` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: behandeling_producten
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `behandeling_producten` (
    `behandeling_id`  INT UNSIGNED  NOT NULL,
    `product_id`      INT UNSIGNED  NOT NULL,
    `aantal_benodigd` DECIMAL(8,3)  NOT NULL,
    PRIMARY KEY (`behandeling_id`, `product_id`),
    CONSTRAINT `fk_bp_behandeling`
        FOREIGN KEY (`behandeling_id`)
        REFERENCES `behandelingen` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_bp_product`
        FOREIGN KEY (`product_id`)
        REFERENCES `producten` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: afspraken
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `afspraken` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `klant_id`       INT UNSIGNED NOT NULL,
    `medewerker_id`  INT UNSIGNED NOT NULL,
    `behandeling_id` INT UNSIGNED NOT NULL,
    `datum`          DATE         NOT NULL,
    `starttijd`      TIME         NOT NULL,
    `eindtijd`       TIME         NOT NULL,
    `status`         VARCHAR(20)  NOT NULL DEFAULT 'gepland',
    `aangemaakt_op`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `gewijzigd_op`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_afspraak_klant`       (`klant_id`),
    KEY `fk_afspraak_medewerker`  (`medewerker_id`),
    KEY `fk_afspraak_behandeling` (`behandeling_id`),
    KEY `idx_datum_starttijd`     (`datum`, `starttijd`),
    CONSTRAINT `fk_afspraak_klant`
        FOREIGN KEY (`klant_id`)       REFERENCES `klanten` (`id`)       ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_afspraak_medewerker`
        FOREIGN KEY (`medewerker_id`)  REFERENCES `medewerkers` (`id`)   ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_afspraak_behandeling`
        FOREIGN KEY (`behandeling_id`) REFERENCES `behandelingen` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: bestellingen
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bestellingen` (
    `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `klant_id`             INT UNSIGNED NOT NULL,
    `orderdatum`           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `verwachte_leverdatum` DATE         DEFAULT NULL,
    `status`               VARCHAR(30)  NOT NULL DEFAULT 'in behandeling',
    `aangemaakt_op`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `gewijzigd_op`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_bestelling_klant` (`klant_id`),
    CONSTRAINT `fk_bestelling_klant`
        FOREIGN KEY (`klant_id`)
        REFERENCES `klanten` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Tabel: bestelregels
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bestelregels` (
    `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `bestelling_id`  INT UNSIGNED  NOT NULL,
    `product_id`     INT UNSIGNED  NOT NULL,
    `aantal`         INT UNSIGNED  NOT NULL,
    `prijs_per_stuk` DECIMAL(8,2)  NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_bestelling_product` (`bestelling_id`, `product_id`),
    CONSTRAINT `fk_br_bestelling`
        FOREIGN KEY (`bestelling_id`)
        REFERENCES `bestellingen` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_br_product`
        FOREIGN KEY (`product_id`)
        REFERENCES `producten` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TESTDATA
-- =====================================================
-- Allergenen (EU-erkende + kappersgerelateerde stoffen)
INSERT INTO `allergenen` (`id`, `naam`) VALUES
    (1,  'Gluten'),
    (2,  'Schaaldieren'),
    (3,  'Eieren'),
    (4,  'Vis'),
    (5,  'Pinda'),
    (6,  'Soja'),
    (7,  'Melk / Lactose'),
    (8,  'Noten'),
    (9,  'Selderij'),
    (10, 'Mosterd'),
    (11, 'Sesam'),
    (12, 'Sulfiet / Zwaveldioxide'),
    (13, 'Lupine'),
    (14, 'Weekdieren'),
    (15, 'Ammoniak'),
    (16, 'Waterstofperoxide'),
    (17, 'Parafenylenediamine (PPD)'),
    (18, 'Resorcinol'),
    (19, 'Parfum / Geurstoffen'),
    (20, 'Propyleenglycol'),
    (21, 'Formaldehyde'),
    (22, 'Methylisothiazolinon'),
    (23, 'Lanoline'),
    (24, 'Latex'),
    (25, 'Nickel');

-- Rollen
INSERT INTO `rollen` (`id`, `naam`) VALUES
    (1, 'eigenaar'),
    (2, 'medewerker'),
    (3, 'klant');

-- Gebruikers
-- Wachtwoorden (plain):
--   lisa@kniploket.nl        -> Admin123
--   erik@kniploket.nl        -> Medew123
--   sophie@example.com       -> Klant123
--   jan.devries@example.com  -> Klant123
--   fatima.yilmaz@example.com-> Klant123
--   marco.smit@example.com   -> Klant123
--   anna.berg@example.com    -> Klant123
--   thomas.kl@example.com    -> Klant123
-- Hashes gegenereerd met password_hash('...', PASSWORD_BCRYPT, ['cost' => 12])
INSERT INTO `gebruikers` (`id`, `naam`, `email`, `wachtwoord`, `rol_id`, `is_actief`) VALUES
    (1, 'Lisa Jansen',       'lisa@kniploket.nl',          '$2y$12$zFNJcGSjm.AN4w0aQ4OwkeIhAfr4yReveZrFSwBmsuEXDMqY58kB.', 1, 1),
    (2, 'Erik de Vries',     'erik@kniploket.nl',          '$2y$12$cwQT7/J7Uybt2PxxYXYz8OpgMviy3MUk2RaJgSmy6YWG7d1XPemBq', 2, 1),
    (3, 'Sophie Bakker',     'sophie@example.com',         '$2y$12$EhJMK8OXaWX8Ni4zs62y2O4O5L.glSWwzrFxDabtj7YL2NZvCbryC', 3, 1),
    (4, 'Jan de Vries',      'jan.devries@example.com',    '$2y$12$EhJMK8OXaWX8Ni4zs62y2O4O5L.glSWwzrFxDabtj7YL2NZvCbryC', 3, 1),
    (5, 'Fatima Yilmaz',     'fatima.yilmaz@example.com',  '$2y$12$EhJMK8OXaWX8Ni4zs62y2O4O5L.glSWwzrFxDabtj7YL2NZvCbryC', 3, 1),
    (6, 'Marco Smit',        'marco.smit@example.com',     '$2y$12$EhJMK8OXaWX8Ni4zs62y2O4O5L.glSWwzrFxDabtj7YL2NZvCbryC', 3, 1),
    (7, 'Anna van den Berg', 'anna.berg@example.com',      '$2y$12$EhJMK8OXaWX8Ni4zs62y2O4O5L.glSWwzrFxDabtj7YL2NZvCbryC', 3, 1),
    (8, 'Thomas Kleijn',     'thomas.kl@example.com',      '$2y$12$EhJMK8OXaWX8Ni4zs62y2O4O5L.glSWwzrFxDabtj7YL2NZvCbryC', 3, 0);

-- Klanten (allergieen kolom is verwijderd — allergenen staan in klant_allergenen)
INSERT INTO `klanten` (`id`, `gebruiker_id`, `adres`, `telefoonnummer`, `wensen`) VALUES
    (1, 3, 'Hoofdstraat 12, 1234 AB Amsterdam',     '0612345678', 'Houdt van natuurlijke producten'),
    (2, 4, 'Kerkstraat 45, 2000 BC Rotterdam',       '0687654321', 'Kort knippen aan de zijkanten'),
    (3, 5, 'Dorpsweg 7, 3500 CD Utrecht',            '+31698765432','Voorkeur voor ammoniakvrije verf'),
    (4, 6, 'Lindelaan 3, 4000 DE Den Haag',          '020-1234567', 'Wil graag tips voor haar thuis'),
    (5, 7, 'Molenlaan 99, 5000 EF Eindhoven',        '0651234567',  'Altijd blowdry na de behandeling'),
    (6, 8, 'Parkweg 22, 6000 FG Maastricht',         '043-9876543', NULL);

-- Klant allergenen (koppeltabel)
-- Sophie: ammoniak + parfum
INSERT INTO `klant_allergenen` (`klant_id`, `allergeen_id`) VALUES
    (1, 15), -- Ammoniak
    (1, 19); -- Parfum / Geurstoffen
-- Jan: geen
-- Fatima: PPD + resorcinol
INSERT INTO `klant_allergenen` (`klant_id`, `allergeen_id`) VALUES
    (3, 17), -- PPD
    (3, 18); -- Resorcinol
-- Marco: latex + nickel
INSERT INTO `klant_allergenen` (`klant_id`, `allergeen_id`) VALUES
    (4, 24), -- Latex
    (4, 25); -- Nickel
-- Anna: gluten + melk (cosmetica-ingrediënten)
INSERT INTO `klant_allergenen` (`klant_id`, `allergeen_id`) VALUES
    (5, 1),  -- Gluten
    (5, 7);  -- Melk / Lactose

-- Medewerkers
INSERT INTO `medewerkers` (`id`, `gebruiker_id`) VALUES
    (1, 1),
    (2, 2);

-- Specialisaties
INSERT INTO `specialisaties` (`id`, `naam`) VALUES
    (1, 'knippen'), (2, 'kleuren'), (3, 'stylen'), (4, 'extensions'), (5, 'haarverzorging');

-- Medewerker specialisaties
INSERT INTO `medewerker_specialisatie` (`medewerker_id`, `specialisatie_id`) VALUES
    (1, 1), (1, 2), (1, 4), (2, 3), (2, 5);

-- Werktijden
INSERT INTO `werktijden` (`medewerker_id`, `dag_van_week`, `starttijd`, `eindtijd`) VALUES
    (1,1,'09:00','17:00'),(1,2,'09:00','17:00'),(1,3,'09:00','17:00'),(1,4,'09:00','17:00'),(1,5,'09:00','17:00'),
    (2,1,'09:00','17:00'),(2,2,'09:00','17:00'),(2,3,'09:00','17:00'),(2,4,'09:00','17:00'),(2,5,'09:00','17:00');

-- Leveranciers
INSERT INTO `leveranciers` (`id`, `naam`) VALUES
    (1,'HairPro Supplies'),(2,'StyleMax BV'),(3,'BeautyHouse'),(4,'ColorWorld');

-- Behandelingen
INSERT INTO `behandelingen` (`id`, `naam`, `prijs`, `duur_minuten`, `beschrijving`) VALUES
    (1,'Knippen dames',35.00,60,'Inclusief wassen en föhnen'),
    (2,'Knippen heren',25.00,30,'Knippen heren'),
    (3,'Kleuren',55.00,90,'Inclusief spoeling en styling'),
    (4,'Stylen',30.00,45,'Föhnen en stylen naar wens'),
    (5,'Extensions',120.00,120,'Inclusief haar en plaatsing');

-- Producten
INSERT INTO `producten` (`id`,`productnaam`,`categorie`,`ean_code`,`voorraad`,`leverancier_id`,`prijs`) VALUES
    (1,'Volume Shampoo','shampoo','8712345678900',15,1,12.50),
    (2,'Hydraterende Conditioner','conditioner','8712345678901',8,1,14.95),
    (3,'Styling Gel Strong','styling','8712345678902',2,2,9.99),
    (4,'Kleurbeschermer Spray','verzorging','8712345678903',20,3,18.50),
    (5,'Permanente Verf 5.0','verf','8712345678904',0,4,22.95);

-- Behandeling producten
INSERT INTO `behandeling_producten` (`behandeling_id`,`product_id`,`aantal_benodigd`) VALUES
    (1,1,0.010),(1,2,0.010),(2,1,0.005),(3,4,0.050),(3,5,0.100),(4,3,0.020),(5,2,0.030);

-- Afspraken
INSERT INTO `afspraken` (`id`,`klant_id`,`medewerker_id`,`behandeling_id`,`datum`,`starttijd`,`eindtijd`,`status`) VALUES
    (1,1,1,1,'2026-07-06','09:00','10:00','gepland'),
    (2,1,1,3,'2026-07-08','11:00','12:30','gepland'),
    (3,2,2,2,'2026-07-07','10:00','10:30','gepland'),
    (4,3,1,3,'2026-07-09','13:00','14:30','gepland'),
    (5,4,2,4,'2026-07-10','14:00','14:45','gepland');

-- Bestellingen
INSERT INTO `bestellingen` (`id`,`klant_id`,`orderdatum`,`verwachte_leverdatum`,`status`) VALUES
    (1,1,'2026-07-01 10:00:00','2026-07-05','gereed');

-- Bestelregels
INSERT INTO `bestelregels` (`id`,`bestelling_id`,`product_id`,`aantal`,`prijs_per_stuk`) VALUES
    (1,1,1,2,12.50),(2,1,3,1,9.99);
