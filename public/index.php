<?php

/**
 * Front controller – enig ingangspunt van de applicatie.
 *
 * Alle verzoeken worden door .htaccess naar dit bestand geleid.
 */

declare(strict_types=1);

// Foutrapportage (schakel af in productie)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Sessie starten met veilige instellingen
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,      // Zet op true bij HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Autoloader (PSR-4 via Composer, of eigen simpele versie)
spl_autoload_register(function (string $klasse): void {
    $pad = dirname(__DIR__) . '/app/' . str_replace(
        ['App\\', '\\'],
        ['',      '/'],
        $klasse
    ) . '.php';

    if (file_exists($pad)) {
        require_once $pad;
    }
});

// Helper functies laden
require_once dirname(__DIR__) . '/app/core/helpers.php';

// Router starten
use App\Core\Router;

$router = new Router();
$router->dispatch();
