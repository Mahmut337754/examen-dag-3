<?php

/**
 * Databaseconfiguratie.
 *
 * Pas de waarden aan naar uw lokale omgeving.
 * Gebruik omgevingsvariabelen of een .env-bestand in productie.
 */

return [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'database' => 'kniploket_tiko1',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
