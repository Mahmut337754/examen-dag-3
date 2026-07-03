<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Fake session
session_start();
$_SESSION['gebruiker_id']   = 1;
$_SESSION['gebruiker_naam'] = 'Lisa';
$_SESSION['gebruiker_rol']  = 'eigenaar';

spl_autoload_register(function (string $klasse): void {
    $pad = __DIR__ . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $klasse) . '.php';
    if (file_exists($pad)) require_once $pad;
});

require_once __DIR__ . '/app/core/helpers.php';

if (!defined('BASE_URL')) define('BASE_URL', '');

try {
    $ctrl = new App\Controllers\DashboardController();
    $ctrl->index();
} catch (Throwable $e) {
    echo "FOUT: " . $e->getMessage() . "\n";
    echo "In: " . $e->getFile() . " regel " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
