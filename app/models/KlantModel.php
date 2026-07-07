<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Logger;
use PDO;

/**
 * Model voor de Klant-tabel.
 * Gebruikt stored procedures voor alle databaseoperaties.
 */
class KlantModel
{
    private PDO    $pdo;
    private Logger $logger;

    public function __construct()
    {
        $this->pdo    = Database::getInstance()->getPdo();
        $this->logger = new Logger();
    }

    // ----------------------------------------------------------------
    // Overzicht – via stored procedures
    // ----------------------------------------------------------------

    /**
     * Haal actieve klanten op met contactgegevens.
     * Gebruikt stored procedure: sp_GetKlantenMetContactGegevens
     */
    public function haalKlantenOp(?string $postcode, int $perPagina, int $offset): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'CALL sp_GetKlantenMetContactGegevens(:postcode, :limit, :offset)'
            );
            $stmt->bindValue(':postcode', $postcode,  PDO::PARAM_STR);
            $stmt->bindValue(':limit',    $perPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset',   $offset,    PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Stored procedure geeft ContactEmail terug — alias naar Email voor views
            return array_map(function (array $r): array {
                $r['Email'] = $r['ContactEmail'] ?? $r['Email'] ?? '';
                return $r;
            }, $rows);
        } catch (\PDOException $e) {
            $this->logger->error('KlantModel::haalKlantenOp – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tel actieve klanten, optioneel gefilterd op postcode.
     * Gebruikt stored procedure: sp_CountKlantenMetContactGegevens
     */
    public function telKlanten(?string $postcode): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'CALL sp_CountKlantenMetContactGegevens(:postcode)'
            );
            $stmt->bindValue(':postcode', $postcode, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['TotaalKlanten'] ?? 0);
        } catch (\PDOException $e) {
            $this->logger->error('KlantModel::telKlanten – ' . $e->getMessage());
            return 0;
        }
    }

    // ----------------------------------------------------------------
    // Detail
    // ----------------------------------------------------------------

    /**
     * Haal één klant op via Klant-id inclusief contactgegevens.
     */
    public function vindOpId(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT k.Id, k.Voornaam, k.Tussenvoegsel, k.Achternaam,
                        k.Relatienummer, k.Bijzonderheden,
                        u.email, u.name AS gebruikersnaam,
                        c.Postcode, c.Plaats, c.Straatnaam,
                        c.Huisnummer, c.Toevoeging, c.Mobiel, c.Email
                 FROM Klant k
                 JOIN users u ON u.id = k.UserId
                 LEFT JOIN KlantPerContact kpc ON kpc.KlantId = k.Id AND kpc.IsActief = 1
                 LEFT JOIN Contact c ON c.Id = kpc.ContactId AND c.IsActief = 1
                 WHERE k.Id = :id AND k.IsActief = 1
                 LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            $this->logger->error('KlantModel::vindOpId – ' . $e->getMessage());
            return null;
        }
    }

    // ----------------------------------------------------------------
    // Wijzigen – via stored procedure
    // ----------------------------------------------------------------

    /**
     * Wijzig klantgegevens via stored procedure sp_UpdateKlantGegevens.
     * Geeft array terug: ['success' => bool, 'message' => string]
     */
    public function wijzigKlant(int $klantId, array $data): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'CALL sp_UpdateKlantGegevens(
                    :klant_id, :voornaam, :tussenvoegsel, :achternaam,
                    :contact_email, :straatnaam, :huisnummer,
                    :toevoeging, :postcode, :plaats, :mobiel, :bijzonderheden,
                    @p_success, @p_message
                )'
            );
            $stmt->execute([
                ':klant_id'       => $klantId,
                ':voornaam'       => $data['voornaam'],
                ':tussenvoegsel'  => $data['tussenvoegsel'] ?: null,
                ':achternaam'     => $data['achternaam'],
                ':contact_email'  => $data['contact_email'],
                ':straatnaam'     => $data['straatnaam'],
                ':huisnummer'     => $data['huisnummer'],
                ':toevoeging'     => $data['toevoeging'] ?: null,
                ':postcode'       => $data['postcode'],
                ':plaats'         => $data['plaats'],
                ':mobiel'         => $data['mobiel'],
                ':bijzonderheden' => $data['bijzonderheden'] ?: null,
            ]);

            $out = $this->pdo->query('SELECT @p_success AS success, @p_message AS message')
                             ->fetch(PDO::FETCH_ASSOC);

            $this->logger->info(
                "KlantModel::wijzigKlant – id={$klantId}, success={$out['success']}"
            );

            return [
                'success' => (bool)(int)($out['success'] ?? 0),
                'message' => $out['message'] ?? '',
            ];
        } catch (\PDOException $e) {
            $this->logger->error('KlantModel::wijzigKlant – ' . $e->getMessage());
            return ['success' => false, 'message' => 'Databasefout bij wijzigen klant.'];
        }
    }

    // ----------------------------------------------------------------
    // Email uniciteit
    // ----------------------------------------------------------------

    /**
     * Controleer of een e-mailadres al in gebruik is door een ANDERE Contact-rij.
     */
    public function emailBestaatAl(string $email, int $huidigContactId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*) FROM Contact
                 WHERE Email = :email AND Id != :contactId AND IsActief = 1'
            );
            $stmt->execute([':email' => $email, ':contactId' => $huidigContactId]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            $this->logger->error('KlantModel::emailBestaatAl – ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Haal het actieve ContactId op voor een klant.
     */
    public function getContactIdVoorKlant(int $klantId): ?int
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT c.Id FROM Contact c
                 JOIN KlantPerContact kpc ON kpc.ContactId = c.Id AND kpc.IsActief = 1
                 WHERE kpc.KlantId = :klantId AND c.IsActief = 1
                 LIMIT 1'
            );
            $stmt->execute([':klantId' => $klantId]);
            $result = $stmt->fetchColumn();
            return $result !== false ? (int)$result : null;
        } catch (\PDOException $e) {
            $this->logger->error('KlantModel::getContactIdVoorKlant – ' . $e->getMessage());
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
            $ip     = $_SERVER['REMOTE_ADDR']   ?? null;

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
            $this->logger->error('TechnischLog schrijven mislukt: ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------------------
    // Aanmaken (behouden voor RegistratieController)
    // ----------------------------------------------------------------

    /**
     * Maak een nieuw Klant-record aan.
     */
    public function maakAan(int $userId, string $volledigeNaam): int
    {
        $delen         = explode(' ', trim($volledigeNaam), 2);
        $voornaam      = $delen[0];
        $achternaam    = $delen[1] ?? $delen[0];
        $relatienummer = 'KL-' . date('Y') . '-' . str_pad((string)$userId, 3, '0', STR_PAD_LEFT);

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
        return (int)$this->pdo->lastInsertId();
    }
}
