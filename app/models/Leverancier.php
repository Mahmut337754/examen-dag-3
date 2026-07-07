<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Logger;
use PDO;
use PDOException;

/**
 * Model voor leveranciersbeheer.
 */
class Leverancier
{
    private PDO      $pdo;
    private Logger   $logger;

    public function __construct()
    {
        $this->pdo    = Database::getInstance()->getPdo();
        $this->logger = new Logger();
    }

    /**
     * Geeft alle leveranciers terug.
     *
     * @return array<int, array<string,mixed>>
     */
    public function alle(): array
    {
        try {
            $sql = 'SELECT id, naam FROM `leveranciers` ORDER BY naam ASC';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logger->error('Leverancier::alle – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Geeft één leverancier op basis van id.
     *
     * @return array<string,mixed>|null
     */
    public function vindOpId(int $id): ?array
    {
        try {
            $sql = 'SELECT id, naam FROM `leveranciers` WHERE id = :id LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $rij = $stmt->fetch();
            return $rij ?: null;
        } catch (PDOException $e) {
            $this->logger->error('Leverancier::vindOpId – ' . $e->getMessage());
            return null;
        }
    }
}