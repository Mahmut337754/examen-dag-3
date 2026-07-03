-- ============================================================
-- Kniploket Tiko - TechnischeLog tabel en seed data
-- ============================================================

USE kniploket_tiko;

-- ------------------------------------------------------------
-- TechnischeLog tabel
-- Doel: audit trail en debugging voor alle modules
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS TechnischeLog (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    LogType ENUM('INFO', 'WARNING', 'ERROR', 'DEBUG') NOT NULL DEFAULT 'INFO',
    Module VARCHAR(100) NOT NULL COMMENT 'Naam van de controller/class die de actie uitvoerde',
    Actie VARCHAR(255) NOT NULL COMMENT 'Korte beschrijving van de uitgevoerde actie',
    Details TEXT NULL COMMENT 'Gedetailleerde JSON-informatie (parameters, resultaten)',
    UserId INT NULL COMMENT 'Gebruiker die de actie uitvoerde (NULL = systeem)',
    IpAdres VARCHAR(45) NULL COMMENT 'IP-adres van de gebruiker (IPv4 of IPv6)',
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    INDEX idx_logtype (LogType),
    INDEX idx_module (Module),
    INDEX idx_datum (DatumAangemaakt),
    INDEX idx_userid (UserId),
    CONSTRAINT fk_technischelog_user
        FOREIGN KEY (UserId) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Technische audit trail voor alle applicatiemodules';

-- ------------------------------------------------------------
-- Seed data: voorbeeldlogs
-- ------------------------------------------------------------
INSERT INTO TechnischeLog (LogType, Module, Actie, Details, UserId, IpAdres) VALUES
('INFO',    'KlantController',     'Klanten overzicht bekeken',                  '{"postcode":null,"pagina":1,"totaal":6}',                                    1, '127.0.0.1'),
('INFO',    'KlantController',     'Klantgegevens bijgewerkt',                   '{"klant_id":4,"velden":["contact_email","mobiel"]}',                         1, '127.0.0.1'),
('WARNING', 'KlantController',     'Poging tot wijzigen met bestaand e-mail',    '{"klant_id":6,"email":"jan.jansen@outlook.com"}',                            1, '127.0.0.1'),
('INFO',    'MedewerkerController','Medewerkers overzicht bekeken',              '{"specialisatie":null,"pagina":1,"totaal":10}',                              1, '127.0.0.1'),
('INFO',    'MedewerkerController','Medewerkergegevens bijgewerkt',              '{"medewerker_id":8,"specialisatie":"Extensions"}',                          1, '127.0.0.1'),
('WARNING', 'MedewerkerController','Wijzigen geweigerd: minderjarige + Permanent','{"medewerker_id":10,"specialisatie":"Permanent","leeftijd":16}',           1, '127.0.0.1'),
('INFO',    'AuthController',      'Gebruiker ingelogd',                         '{"email":"lisa@kniploket.nl","rol":"eigenaar"}',                             1, '127.0.0.1'),
('ERROR',   'Database',            'Connectie mislukt',                          '{"error":"Connection refused","host":"127.0.0.1","port":3306}',           NULL, '127.0.0.1');
