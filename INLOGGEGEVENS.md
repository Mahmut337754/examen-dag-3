# Kniploket Tiko – Inloggegevens

## Testaccounts

### 1. Eigenaar (volledige toegang)
- **E-mail**: `eigenaar@kniplokettiko.nl`
- **Wachtwoord**: `Admin123!`
- **Rol**: eigenaar

### 2. Medewerker (voorbeeld)
- **E-mail**: `fatima@kniplokettiko.nl`
- **Wachtwoord**: `Admin123!`
- **Rol**: medewerker

> Alle medewerkers en klanten gebruiken hetzelfde wachtwoord: `Admin123!`  
> Het wachtwoord-hash in de database: `$2y$10$Y6fjYrntUS.K5v.qBNXr5eWuc1IvAbSUScYsqfwjsh0IkEOyn1te.`

---

## Database importeren (volgorde belangrijk)

```sql
-- Stap 1: Basis schema + testdata
SOURCE database/database.sql;

-- Stap 2: Stored procedures klant
SOURCE database/sp_klant.sql;

-- Stap 3: Stored procedures medewerker
SOURCE database/sp_medewerker.sql;

-- Stap 4: Technische log tabel
SOURCE database/technische_log.sql;
```

Of via phpMyAdmin: importeer de bestanden in bovenstaande volgorde.

---

## Webserver (WAMP virtual host)

```apache
<VirtualHost *:80>
    ServerName examen-dag-3
    DocumentRoot "c:/users/mahmu/desktop/examen-dag-3/examen-dag-3/public"
    <Directory "c:/users/mahmu/desktop/examen-dag-3/examen-dag-3/public/">
        Options +Indexes +Includes +FollowSymLinks +MultiViews
        AllowOverride All
        Require local
    </Directory>
</VirtualHost>
```

Voeg `127.0.0.1 examen-dag-3` toe aan `C:\Windows\System32\drivers\etc\hosts`.

---

## Probleemoplossing

| Fout | Oplossing |
|---|---|
| 500 Internal Server Error | Herstart Apache via WAMP |
| "Databaseverbinding niet beschikbaar" | Start MySQL via WAMP |
| "Stored procedure not found" | Importeer `sp_klant.sql` en `sp_medewerker.sql` |
| 404 bij alle pagina's | Controleer `public/.htaccess` en `mod_rewrite` |
