<?php

namespace App\Core;

/**
 * Eenvoudige front-controller router.
 */
class Router
{
    private array  $routes    = [];
    private Logger $logger;
    private string $basePad   = '';

    public function __construct()
    {
        $this->logger  = new Logger();
        $this->routes  = require dirname(__DIR__) . '/config/routes.php';
        $this->basePad = $this->bepaalBasePad();

        // Stel base-pad beschikbaar als globale sessievariabele voor views
        if (!defined('BASE_URL')) {
            define('BASE_URL', $this->basePad);
        }
    }

    public function dispatch(): void
    {
        $methode = $_SERVER['REQUEST_METHOD'];
        $uri     = $this->normaliseUri($_SERVER['REQUEST_URI'] ?? '/');
        $sleutel = $methode . ' ' . $uri;

        if (!isset($this->routes[$sleutel])) {
            $this->logger->warning("Onbekende route: {$sleutel}");
            http_response_code(404);
            echo '<h1>404 – Pagina niet gevonden</h1>';
            return;
        }

        [$controllerNaam, $actie] = $this->routes[$sleutel];
        $volledigeNaam = 'App\\Controllers\\' . $controllerNaam;

        if (!class_exists($volledigeNaam)) {
            $this->logger->error("Controller niet gevonden: {$volledigeNaam}");
            http_response_code(500);
            echo '<h1>500 – Interne serverfout</h1>';
            return;
        }

        $controller = new $volledigeNaam();

        if (!method_exists($controller, $actie)) {
            $this->logger->error("Actie niet gevonden: {$volledigeNaam}::{$actie}");
            http_response_code(500);
            echo '<h1>500 – Interne serverfout</h1>';
            return;
        }

        $controller->$actie();
    }

    /**
     * Bepaal het base-pad (submap waar index.php in staat).
     * Bijv. /Examen/public of leeg als het de root is.
     */
    private function bepaalBasePad(): string
    {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php');
        return ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/');
    }

    /**
     * Strip het base-pad en normaliseer de URI.
     */
    private function normaliseUri(string $uri): string
    {
        $pad = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Strip base-pad prefix
        if ($this->basePad !== '' && str_starts_with($pad, $this->basePad)) {
            $pad = substr($pad, strlen($this->basePad));
        }

        $pad = '/' . ltrim($pad, '/');

        if ($pad !== '/') {
            $pad = rtrim($pad, '/');
        }

        return $pad;
    }
}
