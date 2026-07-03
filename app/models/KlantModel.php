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
     * Haal alle actieve klanten op met hun user-gegevens.
     */
    public function alleKlanten(): array
    {
        $stmt = $this->pdo->query(
            'SELECT k.*, u.email, u.name AS gebruikersnaam
             FROM Klant k
             JOIN users u ON u.id = k.UserId
             WHERE k.IsActief = 1
             ORDER BY k.Achternaam, k.Voornaam'
        );
        return $stmt->fetchAll();
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
     * Haal één klant op via Klant-id.
     */
    public function vindOpId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT k.*, u.email, u.name AS gebruikersnaam
             FROM Klant k
             JOIN users u ON u.id = k.UserId
             WHERE k.Id = :id AND k.IsActief = 1
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
