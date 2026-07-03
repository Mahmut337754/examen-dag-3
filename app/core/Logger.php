<?php

namespace App\Core;

/**
 * Eenvoudige bestandslogger.
 *
 * Schrijft berichten naar logs/app.log met timestamp en niveau.
 */
class Logger
{
    /** @var string Volledig pad naar het logbestand */
    private string $logBestand;

    /**
     * @param string $logBestand Optioneel pad; standaard logs/app.log
     */
    public function __construct(string $logBestand = '')
    {
        if ($logBestand === '') {
            $logBestand = dirname(__DIR__, 2) . '/logs/app.log';
        }
        $this->logBestand = $logBestand;

        // Zorg dat de logmap bestaat
        $dir = dirname($this->logBestand);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Schrijf een informatieregel naar het logbestand.
     */
    public function info(string $bericht): void
    {
        $this->schrijf('INFO', $bericht);
    }

    /**
     * Schrijf een fout naar het logbestand.
     */
    public function error(string $bericht): void
    {
        $this->schrijf('ERROR', $bericht);
    }

    /**
     * Schrijf een waarschuwing naar het logbestand.
     */
    public function warning(string $bericht): void
    {
        $this->schrijf('WARNING', $bericht);
    }

    /**
     * Interne schrijfmethode.
     */
    private function schrijf(string $niveau, string $bericht): void
    {
        $datum  = date('Y-m-d H:i:s');
        $regel  = "[{$datum}] [{$niveau}] {$bericht}" . PHP_EOL;
        file_put_contents($this->logBestand, $regel, FILE_APPEND | LOCK_EX);
    }
}
