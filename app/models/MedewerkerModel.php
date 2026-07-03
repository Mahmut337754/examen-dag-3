<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Logger;
use PDO;

/**
 * Model voor de Medewerker-tabel.
 * Gebruikt stored procedures voor databaseoperaties.
 */
class MedewerkerModel
{
    private PDO    $pdo;
    private Logger $logger;

    public function __construct()
    {
        $this->pdo    = Database::getInstance()->getPdo();
        $this->logger = new Logger();
    }

    // ----------------------------------------------------------------
    // Haal medewerkers op (via stored procedure)
    // ----------------------------------------------------------------

    /**
     * Haal actieve medewerkers op met contactgegevens.
     * Optioneel gefilterd op specialisatie.
     * Gebruikt stored procedure: sp_GetMedewerkersMetContactGegevens
     */
    public function haalMedewerkersOp(?string $specialisatie, int $perPagina, int $offset): array
    {
        try {
            $stmt = $this->pdo->prepare('CALL sp_GetMedewerkersMetContactGegevens(:specialisatie, :limit, :offset)');
            $stmt->bindValue(':specialisatie', $specialisatie,  PDO::PARAM_STR);
            $stmt->bindValue(':limit',         $perPagina,      PDO::PARAM_INT);
            $stmt->bindValue(':offset',        $offset,         PDO::PARAM_INT);
            $stmt->execute();
            $resultaten = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->logger->info("MedewerkerModel::haalMedewerkersOp – specialisatie={$specialisatie}, limit={$perPagina}, offset={$offset}, gevonden=" . count($resultaten));

            return $resultaten;
        } catch (\PDOException $e) {
            $this->logger->error('MedewerkerModel::haalMedewerkersOp – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tel actieve medewerkers, optioneel gefilterd op specialisatie.
     * Gebruikt stored procedure: sp_CountMedewerkersMetContactGegevens
     */
    public function telMedewerkers(?string $specialisatie): int
    {
        try {
            $stmt = $this->pdo->prepare('CALL sp_CountMedewerkersMetContactGegevens(:specialisatie)');
            $stmt->bindValue(':specialisatie', $specialisatie, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['TotaalMedewerkers'] ?? 0);
        } catch (\PDOException $e) {
            $this->logger->error('MedewerkerModel::telMedewerkers – ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Haal alle unieke specialisaties op van actieve medewerkers.
     * Gebruikt stored procedure: sp_GetUniekeSpecialisaties
     */
    public function getUniekeSpecialisaties(): array
    {
        try {
            $stmt = $this->pdo->prepare('CALL sp_GetUniekeSpecialisaties()');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            $this->logger->error('MedewerkerModel::getUniekeSpecialisaties – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Haal één medewerker op via id inclusief contactgegevens.
     */
    public function vindOpId(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT m.*,
                        u.email AS AccountEmail,
                        u.name AS GebruikersNaam,
                        c.Straatnaam, c.Huisnummer, c.Toevoeging,
                        c.Postcode, c.Plaats, c.Email AS ContactEmail, c.Mobiel
                 FROM Medewerker m
                 JOIN users u ON u.id = m.UserId
                 LEFT JOIN MedewerkerPerContact mpc ON mpc.MedewerkerId = m.Id AND mpc.IsActief = 1
                 LEFT JOIN Contact c ON c.Id = mpc.ContactId AND c.IsActief = 1
                 WHERE m.Id = :id AND m.IsActief = 1
                 LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            $this->logger->error('MedewerkerModel::vindOpId – ' . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------------
    // Technische log
    // ----------------------------------------------------------------

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
            $this->logger->error('TechnischLog schrijven mislukt: ' . $e->getMessage());
        }
    }
}
