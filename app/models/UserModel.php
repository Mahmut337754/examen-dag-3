<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model voor de users-tabel.
 */
class UserModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Zoek een gebruiker op e-mailadres.
     */
    public function vindOpEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = :email AND IsActief = 1 LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Zoek een gebruiker op id.
     */
    public function vindOpId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE id = :id AND IsActief = 1 LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Maak een nieuw gebruikersaccount aan (rol = klant).
     *
     * @return int Nieuw ingevoegd user-id
     */
    public function maakAan(string $naam, string $email, string $wachtwoordHash): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password, role)
             VALUES (:naam, :email, :wachtwoord, :role)'
        );
        $stmt->execute([
            ':naam'      => $naam,
            ':email'     => $email,
            ':wachtwoord'=> $wachtwoordHash,
            ':role'      => 'klant',
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Werk het wachtwoord van een gebruiker bij.
     */
    public function werkWachtwoordBij(int $id, string $wachtwoordHash): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET password = :wachtwoord WHERE id = :id'
        );
        $stmt->execute([
            ':wachtwoord' => $wachtwoordHash,
            ':id'         => $id,
        ]);
    }

    /**
     * Controleer of een e-mailadres al in gebruik is.
     */
    public function emailBestaat(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM users WHERE email = :email'
        );
        $stmt->execute([':email' => $email]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
