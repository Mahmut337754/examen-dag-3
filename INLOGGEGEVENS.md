# Kniploket Tiko – Inloggegevens

## Testaccounts

Gebruik deze inloggegevens om het systeem te testen:

### 1. Eigenaar (volledige toegang)
- **E-mail**: `lisa@kniploket.nl`
- **Wachtwoord**: `Admin123`
- **Rol**: eigenaar

### 2. Medewerker
- **E-mail**: `erik@kniploket.nl`
- **Wachtwoord**: `Medew123`
- **Rol**: medewerker

### 3. Klant (kan niet inloggen op beheerpaneel)
- **E-mail**: `sophie@example.com`
- **Wachtwoord**: `Klant123`
- **Rol**: klant
- **Let op**: Klanten loggen NIET in op het beheerpaneel.

---

## Installatie-instructies

### 1. Database importeren
```bash
# Importeer het schema en testdata
mysql -u root -p < database/database.sql

# Importeer de stored procedures
mysql -u root -p kniploket_tiko < database/procedures/sp_gebruikers.sql
mysql -u root -p kniploket_tiko < database/procedures/sp_klanten.sql
```

### 2. Database-configuratie aanpassen
Bewerk `app/config/database.php` en pas aan:
```php
'host'     => '127.0.0.1',
'username' => 'root',        // Jouw MySQL-gebruikersnaam
'password' => '',            // Jouw MySQL-wachtwoord
'database' => 'kniploket_tiko',
```

### 3. Webserver instellen
- Wijs je webserver (Apache/Nginx) naar de `public/` map
- Zorg dat `mod_rewrite` (Apache) of equivalent (Nginx) actief is
- Voorbeeld voor Apache virtualhost:
  ```apache
  <VirtualHost *:80>
      ServerName kniploket.local
      DocumentRoot "C:/Users/mahmu/Desktop/examen/Examen/public"
      
      <Directory "C:/Users/mahmu/Desktop/examen/Examen/public">
          Options Indexes FollowSymLinks
          AllowOverride All
          Require all granted
      </Directory>
  </VirtualHost>
  ```

### 4. Testen
- Open je browser en ga naar `http://localhost/` (of je geconfigureerde domein)
- Log in met een van de accounts hierboven

---

## Wachtwoorden wijzigen

De huidige wachtwoorden zijn **TESTDATA**. In productie moet je:

1. Nieuwe bcrypt-hashes genereren in PHP:
   ```php
   echo password_hash('jouw_nieuwe_wachtwoord', PASSWORD_BCRYPT);
   ```

2. Update de database:
   ```sql
   UPDATE gebruikers 
   SET wachtwoord = '$2y$10$...' 
   WHERE email = 'lisa@kniploket.nl';
   ```

Of gebruik de ingebouwde "Wachtwoord wijzigen"-functie na inloggen.

---

## Functies per rol

### Eigenaar & Medewerker
- ✅ Inloggen op beheerpaneel
- ✅ Dashboard bekijken met statistieken
- ✅ Klanten beheren (CRUD)
- ✅ Wachtwoord wijzigen
- ⏳ Producten (link aanwezig, functionaliteit volgt later)

### Klant
- ❌ Geen toegang tot beheerpaneel
- ⏳ Toekomstige klantportaal-functionaliteit

---

## Probleemoplossing

### "Databaseverbinding niet beschikbaar"
- Controleer `app/config/database.php`
- Controleer of MySQL draait
- Controleer of database `kniploket_tiko` bestaat

### "404 – Pagina niet gevonden"
- Controleer of `.htaccess` in `public/` aanwezig is
- Controleer of `mod_rewrite` actief is in Apache
- Controleer of de document root naar `public/` wijst

### "Ongeldige inloggegevens"
- Controleer of je de juiste e-mail/wachtwoord gebruikt
- Controleer of de stored procedures correct geïmporteerd zijn
- Check `logs/app.log` voor foutmeldingen

---

**Ontwikkeld voor Kniploket Tiko**  
Branch: `Feature-Klanten`
