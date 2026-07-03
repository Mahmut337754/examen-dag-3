<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model voor de Klant-tabel.
 */
class KlantModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Maak een nieuw Klant-record aan gekoppeld aan een users-id.
     * De naam wordt gesplitst in voor-/achternaam.
     *
     * @return int Nieuw ingevoegd Klant-id
     */
    public function maakAan(int $userId, string $volledigeNaam): int
    {
        // Splits naam eenvoudig op spatie
        $delen      = explode(' ', trim($volledigeNaam), 2);
        $voornaam   = $delen[0];
        $achternaam = $delen[1] ?? $delen[0];

        // Genereer een uniek relatienummer
        $relatienummer = 'KL-' . date('Y') . '-' . str_pad((string)$userId, 4, '0', STR_PAD_LEFT);

        $stmt = $this->pdo->prepare(
            'INSERT INTO Klant (UserId, Voornaam, Achternaam, Relatienummer)
             VALUES (:userId, :voornaam, :achternaam, :relatienummer)'
        );
        $stmt->execute([
            ':userId'        => $userId,
            ':voornaam'      => $voornaam,
            ':achternaam'    => $achternaam,
            ':relatienummer' => $relatienummer,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Haal actieve klanten op met contactgegevens. Optioneel gefilterd op postcode.
     */
    public function haalKlantenOp(?string $postcode, int $perPagina, int $offset): array
    {
        $sql = 'SELECT k.Id, k.Voornaam, k.Tussenvoegsel, k.Achternaam, k.Relatienummer,
                       c.Straatnaam, c.Huisnummer, c.Toevoeging, c.Postcode, c.Plaats, c.Mobiel, c.Email
                FROM Klant k
                LEFT JOIN KlantPerContact kpc ON kpc.KlantId = k.Id AND kpc.IsActief = 1
                LEFT JOIN Contact c ON c.Id = kpc.ContactId AND c.IsActief = 1
                WHERE k.IsActief = 1';

        $params = [];
        if ($postcode !== null) {
            $sql .= ' AND REPLACE(c.Postcode, \' \', \'\') LIKE :postcode';
            $params[':postcode'] = str_replace(' ', '', strtoupper($postcode)) . '%';
        }

        $sql .= ' ORDER BY k.Achternaam, k.Voornaam LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit',  $perPagina, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tel actieve klanten, optioneel gefilterd op postcode.
     */
    public function telKlanten(?string $postcode): int
    {
        $sql = 'SELECT COUNT(*) FROM Klant k
                LEFT JOIN KlantPerContact kpc ON kpc.KlantId = k.Id AND kpc.IsActief = 1
                LEFT JOIN Contact c ON c.Id = kpc.ContactId AND c.IsActief = 1
                WHERE k.IsActief = 1';

        $params = [];
        if ($postcode !== null) {
            $sql .= ' AND REPLACE(c.Postcode, \' \', \'\') LIKE :postcode';
            $params[':postcode'] = str_replace(' ', '', strtoupper($postcode)) . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Haal alle actieve klanten op (legacy – gebruikt door alleKlanten()).
     */
    public function alleKlanten(?string $postcode = null): array
    {
        return $this->haalKlantenOp($postcode, PHP_INT_MAX, 0);
    }

    /**
     * Haal klanten op met contactgegevens, optioneel gefilterd op postcode.
     * Retourneert array met klantgegevens inclusief adres en contact info.
     */
    public function getKlantenMetContactGegevens(?string $postcode = null, int $limit = 6, int $offset = 0): array
    {
        $sql = 'SELECT 
                    k.Id,
                    k.Voornaam,
                    k.Tussenvoegsel,
                    k.Achternaam,
                    k.Relatienummer,
                    c.Straatnaam,
                    c.Huisnummer,
                    c.Toevoeging,
                    c.Postcode,
                    c.Plaats,
                    c.Email AS contact_email,
                    c.Mobiel
                FROM Klant k
                JOIN KlantPerContact kpc ON k.Id = kpc.KlantId
                JOIN Contact c ON kpc.ContactId = c.Id
                WHERE k.IsActief = 1 
                AND kpc.IsActief = 1 
                AND c.IsActief = 1';
        
        $params = [];
        
        if ($postcode !== null && $postcode !== '') {
            $sql .= ' AND c.Postcode = :postcode';
            $params[':postcode'] = $postcode;
        }
        
        $sql .= ' ORDER BY k.Achternaam, k.Voornaam LIMIT :limit OFFSET :offset';
        
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tel totaal aantal klanten met contactgegevens, optioneel gefilterd op postcode.
     */
    public function telKlantenMetContactGegevens(?string $postcode = null): int
    {
        $sql = 'SELECT COUNT(DISTINCT k.Id)
                FROM Klant k
                JOIN KlantPerContact kpc ON k.Id = kpc.KlantId
                JOIN Contact c ON kpc.ContactId = c.Id
                WHERE k.IsActief = 1 
                AND kpc.IsActief = 1 
                AND c.IsActief = 1';
        
        $params = [];
        
        if ($postcode !== null && $postcode !== '') {
            $sql .= ' AND c.Postcode = :postcode';
            $params[':postcode'] = $postcode;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
    }

    /**
     * Controleer of een e-mailadres al in gebruik is door een ANDERE klant's Contact-record.
     * De huidige contactId wordt uitgesloten van de check.
     */
    public function emailBestaatAl(string $email, int $huidigContactId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM Contact
             WHERE Email = :email AND Id != :contactId AND IsActief = 1'
        );
        $stmt->execute([':email' => $email, ':contactId' => $huidigContactId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Wijzig klantgegevens: update de Klant-tabel (Bijzonderheden) en de gekoppelde Contact-rij.
     */
    public function wijzigKlant(int $klantId, array $data): void
    {
        // 1. Bijzonderheden in Klant bijwerken
        $stmt = $this->pdo->prepare(
            'UPDATE Klant SET Bijzonderheden = :bijzonderheden WHERE Id = :id'
        );
        $stmt->execute([
            ':bijzonderheden' => $data['bijzonderheden'],
            ':id'             => $klantId,
        ]);

        // 2. Contactgegevens bijwerken via subquery op KlantPerContact
        $stmt = $this->pdo->prepare(
            'UPDATE Contact c
             JOIN KlantPerContact kpc ON kpc.ContactId = c.Id AND kpc.IsActief = 1
             SET c.Email      = :email,
                 c.Straatnaam = :straatnaam,
                 c.Huisnummer = :huisnummer,
                 c.Toevoeging = :toevoeging,
                 c.Postcode   = :postcode,
                 c.Plaats     = :plaats,
                 c.Mobiel     = :mobiel
             WHERE kpc.KlantId = :klantId AND c.IsActief = 1'
        );
        $stmt->execute([
            ':email'      => $data['contact_email'],
            ':straatnaam' => $data['straatnaam'],
            ':huisnummer' => $data['huisnummer'],
            ':toevoeging' => $data['toevoeging'],
            ':postcode'   => $data['postcode'],
            ':plaats'     => $data['plaats'],
            ':mobiel'     => $data['mobiel'],
            ':klantId'    => $klantId,
        ]);
    }

    /**
     * Haal het actieve ContactId op voor een klant.
     */
    public function getContactIdVoorKlant(int $klantId): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.Id FROM Contact c
             JOIN KlantPerContact kpc ON kpc.ContactId = c.Id AND kpc.IsActief = 1
             WHERE kpc.KlantId = :klantId AND c.IsActief = 1
             LIMIT 1'
        );
        $stmt->execute([':klantId' => $klantId]);
        $result = $stmt->fetchColumn();
        return $result !== false ? (int)$result : null;
    }

    /**
     * Tel het aantal actieve medewerkers.
     */
    public function aantalMedewerkers(): int
    {
        $stmt = $this->pdo->query(
            'SELECT COUNT(*) FROM Medewerker WHERE IsActief = 1'
        );
        return (int) $stmt->fetchColumn();
    }

    /**
     * Haal één klant op via Klant-id inclusief contactgegevens.
     */
    public function vindOpId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT k.*, u.email, u.name AS gebruikersnaam,
                    c.Postcode, c.Plaats, c.Straatnaam, c.Huisnummer, c.Toevoeging, c.Mobiel, c.Email
             FROM Klant k
             JOIN users u ON u.id = k.UserId
             LEFT JOIN KlantPerContact kpc ON kpc.KlantId = k.Id AND kpc.IsActief = 1
             LEFT JOIN Contact c ON c.Id = kpc.ContactId AND c.IsActief = 1
             WHERE k.Id = :id AND k.IsActief = 1
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Schrijf een technische log-entry naar de TechnischeLog tabel.
     *
     * @param string      $type     INFO | WARNING | ERROR | DEBUG
     * @param string      $module   Naam van de module/class
     * @param string      $actie    Beschrijving van de actie
     * @param string|null $details  JSON-string met extra info
     */
    public function logTechnischeActie(
        string  $type,
        string  $module,
        string  $actie,
        ?string $details = null
    ): void {
        try {
            $userId = $_SESSION['gebruiker_id'] ?? null;
            $ip     = $_SERVER['REMOTE_ADDR'] ?? null;

            $stmt = $this->pdo->prepare(
                'INSERT INTO TechnischeLog (LogType, Module, Actie, Details, UserId, IpAdres)
                 VALUES (:logtype, :module, :actie, :details, :userId, :ip)'
            );
            $stmt->execute([
                ':logtype' => $type,
                ':module'  => $module,
                ':actie'   => $actie,
                ':details' => $details,
                ':userId'  => $userId,
                ':ip'      => $ip,
            ]);
        } catch (\PDOException $e) {
            // Schrijf naar bestandslog als DB-log mislukt
            error_log('TechnischLog schrijven mislukt: ' . $e->getMessage());
        }
    }
}
