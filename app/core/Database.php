<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Singleton databaseverbinding via PDO.
 *
 * Gebruik Database::getInstance() om de PDO-instantie op te halen.
 */
class Database
{
    /** @var Database|null Singleton-instantie */
    private static ?Database $instantie = null;

    /** @var PDO De PDO-verbinding */
    private PDO $pdo;

    /**
     * Privéconstructor: laadt config en maakt PDO-verbinding aan.
     */
    private function __construct()
    {
        $config = require dirname(__DIR__) . '/config/database.php';

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            // Log de fout en stop; stel geen wachtwoord bloot in de output
            $logger = new Logger();
            $logger->error('Databaseverbinding mislukt: ' . $e->getMessage());
            http_response_code(500);
            die('Databaseverbinding niet beschikbaar. Controleer de configuratie.');
        }
    }

    /**
     * Geeft de enkele instantie terug.
     */
    public static function getInstance(): Database
    {
        if (self::$instantie === null) {
            self::$instantie = new self();
        }
        return self::$instantie;
    }

    /**
     * Geeft de onderliggende PDO terug.
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Voorkomt klonen van de singleton.
     */
    private function __clone() {}
}
