<?php

/**
 * Genereer een URL met het juiste base-pad.
 *
 * @param string $pad Bijv. '/klanten/verwijderen'
 * @return string     Bijv. '/Examen/public/klanten/verwijderen'
 */
function url(string $pad = '/'): string
{
    $base = defined('BASE_URL') ? BASE_URL : '';
    return $base . '/' . ltrim($pad, '/');
}
