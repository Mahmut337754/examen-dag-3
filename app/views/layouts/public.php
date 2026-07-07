<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kniploket Tiko</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --salon-gold:   #c9a84c;
            --salon-dark:   #1a1a2e;
            --salon-accent: #e8d5a3;
        }

        body { background: #fafaf8; }

        /* Navbar */
        .navbar-tiko { background: var(--salon-dark); }
        .navbar-tiko .navbar-brand { color: var(--salon-gold) !important; font-weight: 700; letter-spacing: 1px; }
        .navbar-tiko .nav-link { color: rgba(255,255,255,.8) !important; }
        .navbar-tiko .nav-link:hover { color: var(--salon-gold) !important; }
        .navbar-tiko .btn-outline-gold {
            color: var(--salon-gold); border-color: var(--salon-gold);
        }
        .navbar-tiko .btn-outline-gold:hover {
            background: var(--salon-gold); color: var(--salon-dark);
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, var(--salon-dark) 0%, #16213e 60%, #0f3460 100%);
            min-height: 92vh;
            display: flex;
            align-items: center;
        }
        .hero-title { color: var(--salon-gold); font-size: clamp(2.5rem, 6vw, 4.5rem); font-weight: 800; }
        .hero-sub   { color: var(--salon-accent); font-size: 1.2rem; }
        .scissors-icon { font-size: 5rem; color: var(--salon-gold); opacity: .9; }

        /* Kaarten */
        .card-portal {
            border: none;
            border-radius: 1.25rem;
            transition: transform .2s, box-shadow .2s;
            cursor: pointer;
        }
        .card-portal:hover { transform: translateY(-6px); box-shadow: 0 1rem 2rem rgba(0,0,0,.15); }

        /* Diensten */
        .service-icon { font-size: 2.5rem; }

        /* Footer */
        footer { background: var(--salon-dark); color: rgba(255,255,255,.6); }
        footer a { color: var(--salon-gold); text-decoration: none; }
    </style>
</head>
<body>

<!-- Navigatiebalk -->
<nav class="navbar navbar-expand-lg navbar-tiko px-3 py-2">
    <a class="navbar-brand" href="/">
        <i class="bi bi-scissors me-2"></i>Kniploket Tiko
    </a>
    <button class="navbar-toggler border-secondary" type="button"
            data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="/#diensten">Diensten</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/#contact">Contact</a>
            </li>
        </ul>
        <div class="d-flex gap-2">
            <a href="/registreren" class="btn btn-outline-gold btn-sm">
                <i class="bi bi-person-plus me-1"></i>Account aanmaken
            </a>
            <a href="/login" class="btn btn-sm" style="background:var(--salon-gold);color:var(--salon-dark);font-weight:600;">
                <i class="bi bi-box-arrow-in-right me-1"></i>Inloggen
            </a>
        </div>
    </div>
</nav>

<!-- Flash-berichten -->
<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show m-3" role="alert">
        <?= $flash['bericht'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Paginainhoud -->
<?= $inhoud ?>

<!-- Footer -->
<footer class="py-4 mt-0">
    <div class="container text-center">
        <p class="mb-1">
            <i class="bi bi-scissors me-2" style="color:var(--salon-gold)"></i>
            <strong style="color:var(--salon-gold)">Kniploket Tiko</strong>
        </p>
        <p class="small mb-0">
            Kalverstraat 1, 1012 NX Amsterdam &nbsp;|&nbsp;
            <a href="tel:+31201234567">020-123 4567</a> &nbsp;|&nbsp;
            <a href="mailto:info@kniploket.nl">info@kniploket.nl</a>
        </p>
        <p class="small mt-2 mb-0 opacity-50">&copy; <?= date('Y') ?> Kniploket Tiko</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmKi69h56ECk3jM28efF/xWv1os0X"
        crossorigin="anonymous"></script>
<script src="/js/validatie.js"></script>
</body>
</html>
