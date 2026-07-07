<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Logger;
use PDO;
use PDOException;

/**
 * Model voor productenbeheer (CRUD via directe PDO-queries met prepared statements).
 */
class Product
{
    private PDO      $pdo;
    private Logger   $logger;

    public function __construct()
    {
        $this->pdo    = Database::getInstance()->getPdo();
        $this->logger = new Logger(dirname(__DIR__, 2) . '/logs/producten.log');
    }

    /**
     * Geeft alle producten terug (JOIN met leveranciers).
     *
     * @return array<int, array<string,mixed>>
     */
    public function overzicht(): array
    {
        try {
            $sql = '
                SELECT
                    p.id,
                    p.productnaam,
                    p.categorie,
                    p.ean_code,
                    p.voorraad,
                    p.prijs,
                    l.naam AS leverancier_naam,
                    p.aangemaakt_op
                FROM `producten` p
                INNER JOIN `leveranciers` l ON l.id = p.leverancier_id
                ORDER BY p.productnaam ASC
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logger->error('Product::overzicht – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Geeft één product op basis van product-id.
     *
     * @return array<string,mixed>|null
     */
    public function vindOpId(int $productId): ?array
    {
        try {
            $sql = '
                SELECT
                    p.id,
                    p.productnaam,
                    p.categorie,
                    p.ean_code,
                    p.voorraad,
                    p.leverancier_id,
                    l.naam AS leverancier_naam,
                    p.prijs
                FROM `producten` p
                INNER JOIN `leveranciers` l ON l.id = p.leverancier_id
                WHERE p.id = :id
                LIMIT 1
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $rij = $stmt->fetch();
            return $rij ?: null;
        } catch (PDOException $e) {
            $this->logger->error('Product::vindOpId – ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Geeft alle leveranciers voor dropdown.
     *
     * @return array<int, array<string,mixed>>
     */
    public function alleLeveranciers(): array
    {
        try {
            $sql = 'SELECT id, naam FROM `leveranciers` ORDER BY naam ASC';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logger->error('Product::alleLeveranciers – ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Voeg een nieuw product toe.
     *
     * @param  array<string,mixed> $data
     * @return array{id:int, fout:string}
     */
    public function aanmaken(array $data): array
    {
        try {
            // Controleer unieke productnaam
            $check = $this->pdo->prepare(
                'SELECT COUNT(*) AS aantal FROM `producten` WHERE `productnaam` = :naam'
            );
            $check->bindValue(':naam', $data['productnaam'], PDO::PARAM_STR);
            $check->execute();
            if ((int)$check->fetch()['aantal'] > 0) {
                return ['id' => 0, 'fout' => 'Productnaam is al in gebruik.'];
            }

            // Controleer unieke EAN-code
            $checkEan = $this->pdo->prepare(
                'SELECT COUNT(*) AS aantal FROM `producten` WHERE `ean_code` = :ean'
            );
            $checkEan->bindValue(':ean', $data['ean_code'], PDO::PARAM_STR);
            $checkEan->execute();
            if ((int)$checkEan->fetch()['aantal'] > 0) {
                return ['id' => 0, 'fout' => 'EAN-code is al in gebruik.'];
            }

            $sql = '
                INSERT INTO `producten` 
                    (`productnaam`, `categorie`, `ean_code`, `voorraad`, `leverancier_id`, `prijs`)
                VALUES 
                    (:naam, :categorie, :ean, :voorraad, :leverancier, :prijs)
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':naam',        $data['productnaam'],    PDO::PARAM_STR);
            $stmt->bindValue(':categorie',   $data['categorie'],      PDO::PARAM_STR);
            $stmt->bindValue(':ean',         $data['ean_code'],       PDO::PARAM_STR);
            $stmt->bindValue(':voorraad',    (int)($data['voorraad'] ?? 0), PDO::PARAM_INT);
            $stmt->bindValue(':leverancier', (int)$data['leverancier_id'], PDO::PARAM_INT);
            $stmt->bindValue(':prijs',       number_format($data['prijs'], 2, '.', ''), PDO::PARAM_STR);
            $stmt->execute();

            $productId = (int) $this->pdo->lastInsertId();
            $this->logger->info("Product aangemaakt id={$productId}");
            return ['id' => $productId, 'fout' => ''];

        } catch (PDOException $e) {
            $this->logger->error('Product::aanmaken – ' . $e->getMessage());
            return ['id' => 0, 'fout' => 'Databasefout bij aanmaken product.'];
        }
    }

    /**
     * Wijzig een bestaand product.
     *
     * @param  int   $productId
     * @param  array<string,mixed> $data
     * @return string Lege string bij succes, foutmelding bij fout
     */
    public function wijzigen(int $productId, array $data): string
    {
        try {
            // Controleer of product bestaat
            $check = $this->pdo->prepare(
                'SELECT COUNT(*) AS aantal FROM `producten` WHERE `id` = :id'
            );
            $check->bindValue(':id', $productId, PDO::PARAM_INT);
            $check->execute();
            if ((int)$check->fetch()['aantal'] === 0) {
                return 'Product niet gevonden.';
            }

            // Controleer unieke productnaam (excl. eigen product)
            $checkNaam = $this->pdo->prepare(
                'SELECT COUNT(*) AS aantal FROM `producten` WHERE `productnaam` = :naam AND `id` != :id'
            );
            $checkNaam->bindValue(':naam', $data['productnaam'], PDO::PARAM_STR);
            $checkNaam->bindValue(':id', $productId, PDO::PARAM_INT);
            $checkNaam->execute();
            if ((int)$checkNaam->fetch()['aantal'] > 0) {
                return 'Productnaam is al in gebruik door een ander product.';
            }

            // Controleer unieke EAN-code (excl. eigen product)
            $checkEan = $this->pdo->prepare(
                'SELECT COUNT(*) AS aantal FROM `producten` WHERE `ean_code` = :ean AND `id` != :id'
            );
            $checkEan->bindValue(':ean', $data['ean_code'], PDO::PARAM_STR);
            $checkEan->bindValue(':id', $productId, PDO::PARAM_INT);
            $checkEan->execute();
            if ((int)$checkEan->fetch()['aantal'] > 0) {
                return 'EAN-code is al in gebruik door een ander product.';
            }

            $sql = '
                UPDATE `producten`
                SET `productnaam`    = :naam,
                    `categorie`      = :categorie,
                    `ean_code`       = :ean,
                    `voorraad`       = :voorraad,
                    `leverancier_id` = :leverancier,
                    `prijs`          = :prijs
                WHERE `id` = :id
            ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':naam',        $data['productnaam'],    PDO::PARAM_STR);
            $stmt->bindValue(':categorie',   $data['categorie'],      PDO::PARAM_STR);
            $stmt->bindValue(':ean',         $data['ean_code'],       PDO::PARAM_STR);
            $stmt->bindValue(':voorraad',    (int)($data['voorraad'] ?? 0), PDO::PARAM_INT);
            $stmt->bindValue(':leverancier', (int)$data['leverancier_id'], PDO::PARAM_INT);
            $stmt->bindValue(':prijs',       number_format($data['prijs'], 2, '.', ''), PDO::PARAM_STR);
            $stmt->bindValue(':id',          $productId,              PDO::PARAM_INT);
            $stmt->execute();

            $this->logger->info("Product id={$productId} gewijzigd.");
            return '';

        } catch (PDOException $e) {
            $this->logger->error('Product::wijzigen – ' . $e->getMessage());
            return 'Databasefout bij wijzigen product.';
        }
    }

    /**
     * Verwijder een product.
     *
     * @return string Lege string bij succes, foutmelding bij fout
     */
    public function verwijderen(int $productId): string
    {
        try {
            // Controleer of product bestaat
            $check = $this->pdo->prepare(
                'SELECT COUNT(*) AS aantal FROM `producten` WHERE `id` = :id'
            );
            $check->bindValue(':id', $productId, PDO::PARAM_INT);
            $check->execute();
            if ((int)$check->fetch()['aantal'] === 0) {
                return 'Product niet gevonden.';
            }

            $this->pdo->beginTransaction();

            // Verwijder eerst behandeling_producten koppelingen
            $this->pdo->prepare(
                'DELETE FROM `behandeling_producten` WHERE `product_id` = :id'
            )->execute([':id' => $productId]);

            // Verwijder bestelregels van dit product
            $this->pdo->prepare(
                'DELETE FROM `bestelregels` WHERE `product_id` = :id'
            )->execute([':id' => $productId]);

            // Verwijder product
            $this->pdo->prepare(
                'DELETE FROM `producten` WHERE `id` = :id'
            )->execute([':id' => $productId]);

            $this->pdo->commit();
            $this->logger->info("Product id={$productId} verwijderd.");
            return '';

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->logger->error('Product::verwijderen – ' . $e->getMessage());
            return 'Databasefout bij verwijderen product: ' . $e->getMessage();
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
                    (SELECT COUNT(*) FROM `producten`) AS aantal_producten,
                    (SELECT COUNT(*) FROM `producten` WHERE `voorraad` = 0) AS producten_uitverkocht,
                    (SELECT COUNT(*) FROM `leveranciers`) AS aantal_leveranciers
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $rij = $stmt->fetch();
            return $rij ?: [];
        } catch (PDOException $e) {
            $this->logger->error('Product::statistieken – ' . $e->getMessage());
            return [];
        }
    }
}