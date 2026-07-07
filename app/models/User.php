<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Logger;
use PDO;
use PDOException;

/**
 * Model voor gebruikersbeheer en authenticatie.
 * Gebruikt directe PDO-queries (geen stored procedures).
 */
class User
{
    private PDO    $pdo;
    private Logger $logger;

    public function __construct()
    {
        $this->pdo    = Database::getInstance()->getPdo();
        $this->logger = new Logger();
    }

    /**
     * Zoek een gebruiker op e-mailadres.
     *
     * @return array<string,mixed>|null
     */
    public function vindOpEmail(string $email): ?array
    {
        try {
            $sql = '
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
                WHERE g.email = :email
                LIMIT 1
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $rij = $stmt->fetch();
            return $rij ?: null;
        } catch (PDOException $e) {
            $this->logger->error('User::vindOpEmail – ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Zoek een gebruiker op ID.
     *
     * @return array<string,mixed>|null
     */
    public function vindOpId(int $id): ?array
    {
        try {
            $sql = '
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
                WHERE g.id = :id
                LIMIT 1
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $rij = $stmt->fetch();
            return $rij ?: null;
        } catch (PDOException $e) {
            $this->logger->error('User::vindOpId – ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Wijzig het wachtwoord van een gebruiker.
     */
    public function wijzigWachtwoord(int $id, string $nieuwWachtwoord): bool
    {
        try {
            $hash = password_hash($nieuwWachtwoord, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare(
                'UPDATE `gebruikers` SET `wachtwoord` = :ww WHERE `id` = :id'
            );
            $stmt->bindValue(':ww', $hash, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id,   PDO::PARAM_INT);
            $stmt->execute();
            $this->logger->info("Wachtwoord gewijzigd voor gebruiker id={$id}");
            return true;
        } catch (PDOException $e) {
            $this->logger->error('User::wijzigWachtwoord – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Controleer of een e-mailadres al bestaat (exclusief eigen gebruiker).
     */
    public function emailBestaat(string $email, int $uitsluitId = 0): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*) AS aantal FROM `gebruikers` WHERE `email` = :email AND `id` != :id'
            );
            $stmt->bindValue(':email', $email,      PDO::PARAM_STR);
            $stmt->bindValue(':id',    $uitsluitId, PDO::PARAM_INT);
            $stmt->execute();
            $rij = $stmt->fetch();
            return ($rij['aantal'] ?? 0) > 0;
        } catch (PDOException $e) {
            $this->logger->error('User::emailBestaat – ' . $e->getMessage());
            return false;
        }
    }
}
