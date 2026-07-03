<?php
// Simulate what Controller.php does
// __DIR__ in Controller.php = app/core
$controllerDir = __DIR__ . '/app/core';
$viewBase = dirname($controllerDir) . '/views/';
echo "Controller __DIR__: $controllerDir\n";
echo "dirname(__DIR__): " . dirname($controllerDir) . "\n";
echo "View base: $viewBase\n";
echo "dashboard/index path: " . $viewBase . "dashboard/index.php\n";
echo "File exists: " . (file_exists($viewBase . "dashboard/index.php") ? "YES" : "NO") . "\n";
echo "Layout path: " . dirname($controllerDir) . '/views/layouts/main.php' . "\n";
echo "Layout exists: " . (file_exists(dirname($controllerDir) . '/views/layouts/main.php') ? "YES" : "NO") . "\n";
