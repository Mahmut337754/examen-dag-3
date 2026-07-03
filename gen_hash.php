<?php
// Genereer hash voor Admin123!
$hash = password_hash('Admin123!', PASSWORD_BCRYPT, ['cost' => 10]);
echo "Hash: " . $hash . PHP_EOL;
echo "Verify: " . (password_verify('Admin123!', $hash) ? 'OK' : 'FAIL') . PHP_EOL;

// Direct de database updaten
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/core/Database.php';

$pdo = App\Core\Database::getInstance()->getPdo();
$stmt = $pdo->prepare('UPDATE users SET password = :hash');
$stmt->execute([':hash' => $hash]);
echo "Rows updated: " . $stmt->rowCount() . PHP_EOL;
echo "Login werkt nu met wachtwoord: Admin123!" . PHP_EOL;
