<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Logger;
use PDO;
use PDOException;

/**
 * Model voor klantenbeheer (CRUD via directe PDO-queries met prepared statements).
 */
class Klant
{
    private PDO      $pdo;
    private Logger   $logger;

    public function __construct()
    {
        $this->pdo    = Database::getInstance()->getPdo();
        $this->logger = new Logger(dirname(__DIR__, 2) . '/logs/klanten.log');
    }

    /**
     * Geeft alle klanten terug (JOIN met gebruikers).
     *
     * @return array<int, array<string,mixed>>
     */
    public function overzicht(): array
    {
        try {
            $sql = '
                SELECT
                    k.id,
                    g.naam,
                    g.email,
                    k.telefoonnummer,
                    k.adres,
                    k.wensen,
                    g.is_actief,
                    g.aangemaakt_op
                FROM `klanten` k
                INNER JOIN `gebruikers` g ON g.id = k.gebruiker_id
                ORDER BY g.naam ASC
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logger->error('Klant::overzicht – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Geeft één klant op basis van klant-id.
     *
     * @return array<string,mixed>|null
     */
    public function vindOpId(int $klantId): ?array
    {
        try {
            $sql = '
                SELECT
                    k.id,
                    k.gebruiker_id,
                    g.naam,
                    g.email,
                    k.telefoonnummer,
                    k.adres,
                    k.wensen,
                    g.is_actief
                FROM `klanten` k
                INNER JOIN `gebruikers` g ON g.id = k.gebruiker_id
                WHERE k.id = :id
                LIMIT 1
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $klantId, PDO::PARAM_INT);
            $stmt->execute();
            $rij = $stmt->fetch();
            return $rij ?: null;
        } catch (PDOException $e) {
            $this->logger->error('Klant::vindOpId – ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Voeg een nieuwe klant toe (gebruiker + klant in transactie).
     *
     * @param  array<string,mixed> $data
     * @return array{id:int, fout:string}
     */
    public function aanmaken(array $data): array
    {
        try {
            // Controleer uniek e-mailadres
            $check = $this->pdo->prepare(
                'SELECT COUNT(*) AS aantal FROM `gebruikers` WHERE `email` = :email'
            );
            $check->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $check->execute();
            if ((int)$check->fetch()['aantal'] > 0) {
                return ['id' => 0, 'fout' => 'E-mailadres is al in gebruik.'];
            }

            $hash = password_hash($data['wachtwoord'], PASSWORD_BCRYPT);

            $this->pdo->beginTransaction();

            // Voeg gebruiker in (rol 'klant' = id 3)
            $sqlGebr = '
                INSERT INTO `gebruikers` (`naam`, `email`, `wachtwoord`, `rol_id`)
                VALUES (:naam, :email, :ww, 3)
            ';
            $stmtGebr = $this->pdo->prepare($sqlGebr);
            $stmtGebr->bindValue(':naam',  $data['naam'],  PDO::PARAM_STR);
            $stmtGebr->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmtGebr->bindValue(':ww',    $hash,          PDO::PARAM_STR);
            $stmtGebr->execute();

            $gebruikerId = (int) $this->pdo->lastInsertId();

            // Voeg klantprofiel in
            $sqlKlant = '
                INSERT INTO `klanten` (`gebruiker_id`, `adres`, `telefoonnummer`, `wensen`)
                VALUES (:gebruiker_id, :adres, :tel, :wens)
            ';
            $stmtKlant = $this->pdo->prepare($sqlKlant);
            $stmtKlant->bindValue(':gebruiker_id', $gebruikerId,               PDO::PARAM_INT);
            $stmtKlant->bindValue(':adres',        $data['adres'] ?? '',        PDO::PARAM_STR);
            $stmtKlant->bindValue(':tel',          $data['telefoonnummer'] ?? '', PDO::PARAM_STR);
            $stmtKlant->bindValue(':wens',         $data['wensen'] ?? '',       PDO::PARAM_STR);
            $stmtKlant->execute();

            $klantId = (int) $this->pdo->lastInsertId();

            // Sla allergenen op
            $allergenenIds = $data['allergenen'] ?? [];
            if (!empty($allergenenIds)) {
                $ins = $this->pdo->prepare(
                    'INSERT INTO `klant_allergenen` (`klant_id`, `allergeen_id`) VALUES (:kid, :aid)'
                );
                foreach ($allergenenIds as $aid) {
                    $ins->bindValue(':kid', $klantId,   PDO::PARAM_INT);
                    $ins->bindValue(':aid', (int)$aid,  PDO::PARAM_INT);
                    $ins->execute();
                }
            }

            $this->pdo->commit();
            $this->logger->info("Klant aangemaakt id={$klantId}");
            return ['id' => $klantId, 'fout' => ''];

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->logger->error('Klant::aanmaken – ' . $e->getMessage());
            return ['id' => 0, 'fout' => 'Databasefout bij aanmaken klant.'];
        }
    }

    /**
     * Wijzig een bestaande klant.
     *
     * @param  array<string,mixed> $data
     * @return string Lege string bij succes, foutmelding bij fout
     */
    public function wijzigen(int $klantId, array $data): string
    {
        try {
            // Haal gebruiker_id op
            $stmtId = $this->pdo->prepare(
                'SELECT `gebruiker_id` FROM `klanten` WHERE `id` = :id LIMIT 1'
            );
            $stmtId->bindValue(':id', $klantId, PDO::PARAM_INT);
            $stmtId->execute();
            $rij = $stmtId->fetch();

            if (!$rij) {
                return 'Klant niet gevonden.';
            }
            $gebruikerId = (int) $rij['gebruiker_id'];

            // Controleer uniek e-mailadres (excl. eigen gebruiker)
            $check = $this->pdo->prepare(
                'SELECT COUNT(*) AS aantal FROM `gebruikers` WHERE `email` = :email AND `id` != :id'
            );
            $check->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $check->bindValue(':id',    $gebruikerId,   PDO::PARAM_INT);
            $check->execute();
            if ((int)$check->fetch()['aantal'] > 0) {
                return 'E-mailadres is al in gebruik door een andere gebruiker.';
            }

            $this->pdo->beginTransaction();

            // Update gebruiker
            $stmtGebr = $this->pdo->prepare(
                'UPDATE `gebruikers` SET `naam` = :naam, `email` = :email WHERE `id` = :id'
            );
            $stmtGebr->bindValue(':naam',  $data['naam'],  PDO::PARAM_STR);
            $stmtGebr->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmtGebr->bindValue(':id',    $gebruikerId,   PDO::PARAM_INT);
            $stmtGebr->execute();

            // Update wachtwoord alleen als ingevuld
            if (!empty($data['wachtwoord'])) {
                $hash = password_hash($data['wachtwoord'], PASSWORD_BCRYPT);
                $stmtWw = $this->pdo->prepare(
                    'UPDATE `gebruikers` SET `wachtwoord` = :ww WHERE `id` = :id'
                );
                $stmtWw->bindValue(':ww', $hash,        PDO::PARAM_STR);
                $stmtWw->bindValue(':id', $gebruikerId, PDO::PARAM_INT);
                $stmtWw->execute();
            }

            // Update klantprofiel
            $stmtKlant = $this->pdo->prepare('
                UPDATE `klanten`
                SET `adres`          = :adres,
                    `telefoonnummer` = :tel,
                    `wensen`         = :wens
                WHERE `id` = :id
            ');
            $stmtKlant->bindValue(':adres', $data['adres'] ?? '',          PDO::PARAM_STR);
            $stmtKlant->bindValue(':tel',   $data['telefoonnummer'] ?? '', PDO::PARAM_STR);
            $stmtKlant->bindValue(':wens',  $data['wensen'] ?? '',         PDO::PARAM_STR);
            $stmtKlant->bindValue(':id',    $klantId,                      PDO::PARAM_INT);
            $stmtKlant->execute();

            // Vervang allergenen
            $delAll = $this->pdo->prepare(
                'DELETE FROM `klant_allergenen` WHERE `klant_id` = :id'
            );
            $delAll->bindValue(':id', $klantId, PDO::PARAM_INT);
            $delAll->execute();

            $allergenenIds = $data['allergenen'] ?? [];
            if (!empty($allergenenIds)) {
                $ins = $this->pdo->prepare(
                    'INSERT INTO `klant_allergenen` (`klant_id`, `allergeen_id`) VALUES (:kid, :aid)'
                );
                foreach ($allergenenIds as $aid) {
                    $ins->bindValue(':kid', $klantId,  PDO::PARAM_INT);
                    $ins->bindValue(':aid', (int)$aid, PDO::PARAM_INT);
                    $ins->execute();
                }
            }

            $this->pdo->commit();
            $this->logger->info("Klant id={$klantId} gewijzigd.");
            return '';

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->logger->error('Klant::wijzigen – ' . $e->getMessage());
            return 'Databasefout bij wijzigen klant.';
        }
    }

    /**
     * Verwijder een klant met alle gerelateerde data.
     *
     * Verwijdervolgorde:
     * 1. bestelregels  (via bestellingen FK)
     * 2. bestellingen
     * 3. afspraken
     * 4. klant_allergenen
     * 5. klanten
     * 6. gebruikers
     *
     * @return string Lege string bij succes, foutmelding bij fout
     */
    public function verwijderen(int $klantId): string
    {
        try {
            // Haal gebruiker_id op
            $stmtId = $this->pdo->prepare(
                'SELECT `gebruiker_id` FROM `klanten` WHERE `id` = :id LIMIT 1'
            );
            $stmtId->bindValue(':id', $klantId, PDO::PARAM_INT);
            $stmtId->execute();
            $rij = $stmtId->fetch();

            if (!$rij) {
                return 'Klant niet gevonden.';
            }
            $gebruikerId = (int) $rij['gebruiker_id'];

            $this->pdo->beginTransaction();

            // 1. Bestelregels van bestellingen van deze klant
            $this->pdo->prepare('
                DELETE br FROM `bestelregels` br
                INNER JOIN `bestellingen` b ON b.id = br.bestelling_id
                WHERE b.klant_id = :id
            ')->execute([':id' => $klantId]);

            // 2. Bestellingen
            $this->pdo->prepare(
                'DELETE FROM `bestellingen` WHERE `klant_id` = :id'
            )->execute([':id' => $klantId]);

            // 3. Afspraken
            $this->pdo->prepare(
                'DELETE FROM `afspraken` WHERE `klant_id` = :id'
            )->execute([':id' => $klantId]);

            // 4. Allergenen-koppeling
            $this->pdo->prepare(
                'DELETE FROM `klant_allergenen` WHERE `klant_id` = :id'
            )->execute([':id' => $klantId]);

            // 5. Klantprofiel
            $this->pdo->prepare(
                'DELETE FROM `klanten` WHERE `id` = :id'
            )->execute([':id' => $klantId]);

            // 6. Gebruikersaccount (geen cascade → expliciet verwijderen)
            $this->pdo->prepare(
                'DELETE FROM `gebruikers` WHERE `id` = :id'
            )->execute([':id' => $gebruikerId]);

            $this->pdo->commit();
            $this->logger->info("Klant id={$klantId} (gebruiker id={$gebruikerId}) verwijderd.");
            return '';

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->logger->error('Klant::verwijderen – ' . $e->getMessage());
            return 'Databasefout bij verwijderen klant: ' . $e->getMessage();
        }
    }

    /**
     * Haal dashboardstatistieken op.
     *
     * @return array<string,int>
     */
    public function statistieken(): array
    {
        try {
            $sql = "
                SELECT
                    (SELECT COUNT(*) FROM `klanten`)                               AS aantal_klanten,
                    (SELECT COUNT(*) FROM `afspraken` WHERE `status` = 'gepland') AS geplande_afspraken,
                    (SELECT COUNT(*) FROM `medewerkers`)                           AS aantal_medewerkers,
                    (SELECT COUNT(*) FROM `producten` WHERE `voorraad` = 0)        AS producten_uitverkocht
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rij = $stmt->fetch();
            return $rij ?: [];
        } catch (PDOException $e) {
            $this->logger->error('Klant::statistieken – ' . $e->getMessage());
            return [];
        }
    }
}
