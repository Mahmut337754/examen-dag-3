<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Logger;
use PDO;
use PDOException;

/**
 * Model voor allergenen.
 */
class Allergeen
{
    private PDO    $pdo;
    private Logger $logger;

    public function __construct()
    {
        $this->pdo    = Database::getInstance()->getPdo();
        $this->logger = new Logger();
    }

    /**
     * Geeft alle allergenen terug, gesorteerd op naam.
     *
     * @return array<int, array{id:int, naam:string}>
     */
    public function alle(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT `id`, `naam` FROM `allergenen` ORDER BY `naam` ASC');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logger->error('Allergeen::alle – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Geeft de allergeen-IDs terug van een klant.
     *
     * @return int[]
     */
    public function vanKlant(int $klantId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT `allergeen_id` FROM `klant_allergenen` WHERE `klant_id` = :id'
            );
            $stmt->bindValue(':id', $klantId, PDO::PARAM_INT);
            $stmt->execute();
            return array_column($stmt->fetchAll(), 'allergeen_id');
        } catch (PDOException $e) {
            $this->logger->error('Allergeen::vanKlant – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Geeft de allergeen-namen terug van een klant.
     *
     * @return string[]
     */
    public function namenVanKlant(int $klantId): array
    {
        try {
            $sql = '
                SELECT a.naam
                FROM `klant_allergenen` ka
                INNER JOIN `allergenen` a ON a.id = ka.allergeen_id
                WHERE ka.klant_id = :id
                ORDER BY a.naam ASC
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $klantId, PDO::PARAM_INT);
            $stmt->execute();
            return array_column($stmt->fetchAll(), 'naam');
        } catch (PDOException $e) {
            $this->logger->error('Allergeen::namenVanKlant – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Sla de allergenen van een klant op (verwijder oud, voeg nieuw in).
     *
     * @param int[]  $allergenenIds
     */
    public function opslaanVoorKlant(int $klantId, array $allergenenIds): void
    {
        try {
            // Verwijder bestaande koppelingen
            $del = $this->pdo->prepare('DELETE FROM `klant_allergenen` WHERE `klant_id` = :id');
            $del->bindValue(':id', $klantId, PDO::PARAM_INT);
            $del->execute();

            // Voeg nieuwe koppelingen in
            if (!empty($allergenenIds)) {
                $ins = $this->pdo->prepare(
                    'INSERT INTO `klant_allergenen` (`klant_id`, `allergeen_id`) VALUES (:kid, :aid)'
                );
                foreach ($allergenenIds as $allergeen_id) {
                    $ins->bindValue(':kid', $klantId,               PDO::PARAM_INT);
                    $ins->bindValue(':aid', (int)$allergeen_id,     PDO::PARAM_INT);
                    $ins->execute();
                }
            }
        } catch (PDOException $e) {
            $this->logger->error('Allergeen::opslaanVoorKlant – ' . $e->getMessage());
        }
    }
}
