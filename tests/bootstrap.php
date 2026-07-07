<?php

/**
 * PHPUnit bootstrap bestand.
 * Laadt autoloader en initieert testomgeving.
 */

declare(strict_types=1);

// Autoloader inladen
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Stel foutrapportage in voor tests
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Start sessie als deze nog niet actief is (voor controller tests)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definieer BASE_URL voor tests
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
