<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Toont de publieke homepage.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $flash = $this->getFlash();
        $this->view('home/index', compact('flash'));
    }
}
