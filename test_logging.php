<?php

/**
 * Simple test script to verify separate log files for klanten and producten
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Klant;
use App\Models\Product;

echo "=== Testing Separate Log Files ===\n\n";

// Test Klant model logging
echo "Testing Klant model...\n";
$klantModel = new Klant();
try {
    // This will try to connect to database and log any errors to klanten.log
    $klanten = $klantModel->overzicht();
    echo "✓ Klant model initialized successfully\n";
    echo "  Logs should be written to: logs/klanten.log\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test Product model logging
echo "Testing Product model...\n";
$productModel = new Product();
try {
    // This will try to connect to database and log any errors to producten.log
    $producten = $productModel->overzicht();
    echo "✓ Product model initialized successfully\n";
    echo "  Logs should be written to: logs/producten.log\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Check the following log files:\n";
echo "  - logs/klanten.log\n";
echo "  - logs/producten.log\n";