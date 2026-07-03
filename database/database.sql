-- ============================================================
-- Kniploket Tiko - Database Creation Script

DROP DATABASE IF EXISTS kniploket_tiko;
CREATE DATABASE kniploket_tiko
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE kniploket_tiko;

-- ------------------------------------------------------------
-- Table: users
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at DATETIME NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    IsActief BIT(1) NOT NULL DEFAULT 1,
    Opmerking VARCHAR(255) NULL DEFAULT NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: Klant
-- ------------------------------------------------------------
CREATE TABLE Klant (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    UserId INT NOT NULL,
    Voornaam VARCHAR(100) NOT NULL,
    Tussenvoegsel VARCHAR(50) NULL DEFAULT NULL,
    Achternaam VARCHAR(100) NOT NULL,
    Relatienummer VARCHAR(50) NOT NULL UNIQUE,
    Bijzonderheden TEXT NULL DEFAULT NULL,
    IsActief BIT(1) NOT NULL DEFAULT 1,
    Opmerking VARCHAR(255) NULL DEFAULT NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_klant_user FOREIGN KEY (UserId) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: Medewerker
-- ------------------------------------------------------------
CREATE TABLE Medewerker (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    UserId INT NOT NULL,
    Voornaam VARCHAR(100) NOT NULL,
    Tussenvoegsel VARCHAR(50) NULL DEFAULT NULL,
    Achternaam VARCHAR(100) NOT NULL,
    Specialisatie VARCHAR(100) NOT NULL,
    Geboortedatum DATE NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT 1,
    Opmerking VARCHAR(255) NULL DEFAULT NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_medewerker_user FOREIGN KEY (UserId) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: Contact
-- ------------------------------------------------------------
CREATE TABLE Contact (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Straatnaam VARCHAR(255) NOT NULL,
    Huisnummer VARCHAR(20) NOT NULL,
    Toevoeging VARCHAR(20) NULL DEFAULT NULL,
    Postcode VARCHAR(10) NOT NULL,
    Plaats VARCHAR(100) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    Mobiel VARCHAR(20) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT 1,
    Opmerking VARCHAR(255) NULL DEFAULT NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: KlantPerContact
-- ------------------------------------------------------------
CREATE TABLE KlantPerContact (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    KlantId INT NOT NULL,
    ContactId INT NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT 1,
    Opmerking VARCHAR(255) NULL DEFAULT NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_klantpercontact_klant FOREIGN KEY (KlantId) REFERENCES Klant(Id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_klantpercontact_contact FOREIGN KEY (ContactId) REFERENCES Contact(Id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_klant_contact (KlantId, ContactId)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: MedewerkerPerContact
-- ------------------------------------------------------------
CREATE TABLE MedewerkerPerContact (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    MedewerkerId INT NOT NULL,
    ContactId INT NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT 1,
    Opmerking VARCHAR(255) NULL DEFAULT NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_medewerkerpercontact_medewerker FOREIGN KEY (MedewerkerId) REFERENCES Medewerker(Id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_medewerkerpercontact_contact FOREIGN KEY (ContactId) REFERENCES Contact(Id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_medewerker_contact (MedewerkerId, ContactId)
) ENGINE=InnoDB;

-- ============================================================
-- Insert Test Data
-- ============================================================

-- Users
INSERT INTO users (id, name, email, email_verified_at, password, role, remember_token, created_at, updated_at, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 'Lisa van Kniploket', 'lisa@kniploket.nl', NULL, '$2y$12$isqtV5oA4McYaUzLg/mOYehrPHn5ZaACtTA8iOicowzBfn7kgOX1e', 'eigenaar', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(2, 'Erik de Kapper', 'erik@kniploket.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(3, 'Sanne de Vries', 'sanne.devries@kniplokettiko.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(4, 'Mohamed El Idrissi', 'mohamed.elidrissi@kniplokettiko.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(5, 'Lisa van Dijk', 'lisa.vandijk@kniplokettiko.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(6, 'Youssef Benali', 'youssef.benali@kniplokettiko.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(7, 'Noor Bakker', 'noor.bakker@kniplokettiko.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(8, 'Kevin Smit', 'kevin.smit@kniplokettiko.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(9, 'Aylin Demir', 'aylin.demir@kniplokettiko.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(10, 'Tom Verhoeven', 'tom.verhoeven@kniplokettiko.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(11, 'Romy Jacobs', 'romy.jacobs@kniplokettiko.nl', NULL, '$2y$12$ecoHj1bPiqhZtV0IzdIDN.erSQWprAiFIgX0M9QbAN.dO90S514Ui', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(12, 'Piet van Loenen', 'piet.van.loenen@gmail.com', NULL, '$2y$12$q6sJssnJzzDuWyi.fPNd/ulSl3HA20PpQAyfe2C3a70zV79cxSUqu', 'klant', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(13, 'Jan Jansen', 'jan.jansen@outlook.com', NULL, '$2y$12$q6sJssnJzzDuWyi.fPNd/ulSl3HA20PpQAyfe2C3a70zV79cxSUqu', 'klant', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(14, 'Saskia de Boer', 'saskia.deboer@yahoo.com', NULL, '$2y$12$q6sJssnJzzDuWyi.fPNd/ulSl3HA20PpQAyfe2C3a70zV79cxSUqu', 'klant', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(15, 'Ahmed Mansouri', 'ahmed.mansouri@icloud.com', NULL, '$2y$12$q6sJssnJzzDuWyi.fPNd/ulSl3HA20PpQAyfe2C3a70zV79cxSUqu', 'klant', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(16, 'Marieke van den Berg', 'marieke.vandenberg@ziggo.nl', NULL, '$2y$12$q6sJssnJzzDuWyi.fPNd/ulSl3HA20PpQAyfe2C3a70zV79cxSUqu', 'klant', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(17, 'Daan Visser', 'daan.visser@live.nl', NULL, '$2y$12$q6sJssnJzzDuWyi.fPNd/ulSl3HA20PpQAyfe2C3a70zV79cxSUqu', 'klant', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(18, 'Sophie Klant', 'sophie@example.com', NULL, '$2y$12$q6sJssnJzzDuWyi.fPNd/ulSl3HA20PpQAyfe2C3a70zV79cxSUqu', 'klant', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30');

-- Klant
INSERT INTO Klant (Id, UserId, Voornaam, Tussenvoegsel, Achternaam, Relatienummer, Bijzonderheden, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 12, 'Piet', 'van', 'Loenen', 'KL-2026-001', 'Voorkeur voor ochtendafspraken.', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(2, 13, 'Jan', NULL, 'Jansen', 'KL-2026-002', 'Allergie voor sterk geparfumeerde producten.', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(3, 14, 'Saskia', 'de', 'Boer', 'KL-2026-003', 'Komt elke zes weken.', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(4, 15, 'Ahmed', NULL, 'Mansouri', 'KL-2026-004', 'Wil strakke fade.', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(5, 16, 'Marieke', 'van den', 'Berg', 'KL-2026-005', 'Gevoelige hoofdhuid.', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(6, 17, 'Daan', NULL, 'Visser', 'KL-2026-006', 'Liefst einde middag.', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30');

-- Medewerker
INSERT INTO Medewerker (Id, UserId, Voornaam, Tussenvoegsel, Achternaam, Specialisatie, Geboortedatum, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 2, 'Fatima', NULL, 'El Amrani', 'Knippen', '1988-04-12', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(2, 3, 'Sanne', 'de', 'Vries', 'Kleuren', '1996-09-25', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(3, 4, 'Mohamed', NULL, 'El Idrissi', 'Extensions', '1992-02-14', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(4, 5, 'Lisa', 'van', 'Dijk', 'Stylen', '1998-07-08', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(5, 6, 'Youssef', NULL, 'Benali', 'Knippen', '1990-11-30', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(6, 7, 'Noor', NULL, 'Bakker', 'Kleuren', '1997-05-21', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(7, 8, 'Kevin', NULL, 'Smit', 'Extensions', '2001-03-17', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(8, 9, 'Aylin', NULL, 'Demir', 'Stylen', '1999-12-04', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(9, 10, 'Tom', NULL, 'Verhoeven', 'Knippen', '1995-08-19', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(10, 11, 'Romy', NULL, 'Jacobs', 'Knippen', '2010-01-15', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30');

-- Contact
INSERT INTO Contact (Id, Straatnaam, Huisnummer, Toevoeging, Postcode, Plaats, Email, Mobiel, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 'Kanaalstraat', '12', NULL, '3511AB', 'Utrecht', 'fatima@kniplokettiko.nl', '0612345678', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(2, 'Croeselaan', '101', NULL, '3521BJ', 'Utrecht', 'sanne.devries@kniplokettiko.nl', '0611111111', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(3, 'Amsterdamsestraatweg', '223', NULL, '3551CG', 'Utrecht', 'mohamed.elidrissi@kniplokettiko.nl', '0611111112', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(4, 'Maliebaan', '17', NULL, '3581CC', 'Utrecht', 'lisa.vandijk@kniplokettiko.nl', '0611111113', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(5, 'Balijelaan', '63', NULL, '3521GM', 'Utrecht', 'youssef.benali@kniplokettiko.nl', '0611111114', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(6, 'Nachtegaalstraat', '95', NULL, '3581AE', 'Utrecht', 'noor.bakker@kniplokettiko.nl', '0611111115', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(7, 'Bernardlaan', '7', NULL, '3527GA', 'Utrecht', 'kevin.smit@kniplokettiko.nl', '0611111116', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(8, 'Laan van Nieuw-Guinea', '141', NULL, '3531JE', 'Utrecht', 'aylin.demir@kniplokettiko.nl', '0611111117', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(9, 'Marnixlaan', '205', NULL, '3552HD', 'Utrecht', 'tom.verhoeven@kniplokettiko.nl', '0611111118', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(10, 'Haroekoeplein', '29', NULL, '3531WK', 'Utrecht', 'romy.jacobs@kniplokettiko.nl', '0611111119', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(11, 'Oudegracht', '88', 'A', '3512AB', 'Utrecht', 'piet.van.loenen@gmail.com', '+31 6 1234 61 71', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(12, 'Biltstraat', '44', NULL, '3572BC', 'Utrecht', 'jan.jansen@outlook.com', '+31 6 1234 61 72', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(13, 'Merelstraat', '12', NULL, '3514CN', 'Utrecht', 'saskia.deboer@yahoo.com', '+31 6 1234 61 73', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(14, 'Winkel van Sinkelstraat', '4', NULL, '3511KV', 'Utrecht', 'ahmed.mansouri@icloud.com', '+31 6 1234 61 74', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(15, 'Adelaarstraat', '50', NULL, '3514CH', 'Utrecht', 'marieke.vandenberg@ziggo.nl', '+31 6 1234 61 75', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(16, 'Vleutenseweg', '73', NULL, '3532HA', 'Utrecht', 'daan.visser@live.nl', '+31 6 1234 61 76', 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30');

-- KlantPerContact
INSERT INTO KlantPerContact (Id, KlantId, ContactId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 11, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(2, 2, 12, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(3, 3, 13, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(4, 4, 14, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(5, 5, 15, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(6, 6, 16, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30');

-- MedewerkerPerContact
INSERT INTO MedewerkerPerContact (Id, MedewerkerId, ContactId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 1, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(2, 2, 2, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(3, 3, 3, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(4, 4, 4, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(5, 5, 5, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(6, 6, 6, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(7, 7, 7, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(8, 8, 8, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(9, 9, 9, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(10, 10, 10, 1, NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30');

-- Reset auto-increment to correct values (optional but recommended)
ALTER TABLE users AUTO_INCREMENT = 19;
ALTER TABLE Klant AUTO_INCREMENT = 7;
ALTER TABLE Medewerker AUTO_INCREMENT = 11;
ALTER TABLE Contact AUTO_INCREMENT = 17;
ALTER TABLE KlantPerContact AUTO_INCREMENT = 7;
ALTER TABLE MedewerkerPerContact AUTO_INCREMENT = 11;