<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kniploket Tiko</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ── Reset & basis ── */
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            background: #f0f0f0;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════════
           NAVBAR
        ══════════════════════════════════════════ */
        .kt-nav {
            background: #c0392b;
            min-height: 48px;
            padding: 0 1rem;
            display: flex;
            align-items: stretch;
            position: relative;
            z-index: 100;
        }

        /* Merk */
        .kt-brand {
            color: #fff;
            font-weight: 800;
            font-size: .95rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding-right: 1.5rem;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .kt-brand:hover { color: #fff; }

        /* Nav-links (desktop) */
        .kt-links {
            display: flex;
            align-items: stretch;
            list-style: none;
            margin: 0;
            padding: 0;
            flex: 1;
        }
        .kt-links li a {
            color: rgba(255,255,255,.9);
            text-decoration: none;
            font-size: .82rem;
            font-weight: 500;
            padding: 0 .75rem;
            display: flex;
            align-items: center;
            height: 100%;
            border-bottom: 3px solid transparent;
            white-space: nowrap;
            transition: background .15s, border-color .15s;
        }
        .kt-links li a:hover { background: rgba(0,0,0,.12); color: #fff; }
        .kt-links li a.actief {
            background: rgba(0,0,0,.18);
            border-bottom-color: #fff;
            color: #fff;
        }

        /* Rechter blok (gebruiker + uitloggen) */
        .kt-right {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-left: auto;
            flex-shrink: 0;
        }
        .kt-user {
            color: rgba(255,255,255,.85);
            font-size: .8rem;
            white-space: nowrap;
        }

        /* Uitloggen knop — subtiel, niet te donker */
        .kt-logout {
            background: rgba(255,255,255,.15);   /* licht wit-transparant */
            color: #fff;
            border: 1px solid rgba(255,255,255,.45);
            border-radius: .25rem;
            font-size: .78rem;
            padding: .22rem .75rem;
            text-decoration: none;
            white-space: nowrap;
            transition: background .15s;
        }
        .kt-logout:hover {
            background: rgba(255,255,255,.28);
            color: #fff;
        }

        /* ── Hamburger knop (verborgen op desktop) ── */
        .kt-hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: .4rem .5rem;
            margin-left: auto;
            flex-direction: column;
            gap: 5px;
            align-self: center;
        }
        .kt-hamburger span {
            display: block;
            width: 22px;
            height: 2px;
            background: #fff;
            border-radius: 2px;
            transition: transform .25s, opacity .25s;
        }
        /* Animatie: hamburger → X */
        .kt-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .kt-hamburger.open span:nth-child(2) { opacity: 0; }
        .kt-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* ── Mobile dropdown menu ── */
        .kt-mobile-menu {
            display: none;
            background: #a93226;       /* iets donkerder rood */
            list-style: none;
            margin: 0;
            padding: .4rem 0;
            position: absolute;
            top: 48px;
            left: 0;
            right: 0;
            z-index: 99;
            box-shadow: 0 4px 12px rgba(0,0,0,.2);
        }
        .kt-mobile-menu.open { display: block; }
        .kt-mobile-menu li a {
            display: block;
            color: rgba(255,255,255,.9);
            text-decoration: none;
            font-size: .87rem;
            font-weight: 500;
            padding: .55rem 1.25rem;
            border-left: 3px solid transparent;
            transition: background .12s;
        }
        .kt-mobile-menu li a:hover,
        .kt-mobile-menu li a.actief {
            background: rgba(0,0,0,.15);
            border-left-color: #fff;
            color: #fff;
        }
        .kt-mobile-menu .kt-mobile-divider {
            border-top: 1px solid rgba(255,255,255,.18);
            margin: .35rem 0;
        }
        .kt-mobile-menu .kt-mobile-user {
            padding: .4rem 1.25rem;
            font-size: .78rem;
            color: rgba(255,255,255,.65);
        }

        /* ══════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
        ══════════════════════════════════════════ */
        @media (max-width: 900px) {
            .kt-links { display: none; }
            .kt-right  { display: none; }
            .kt-hamburger { display: flex; }
        }

        /* ══════════════════════════════════════════
           CONTENT + FOOTER (sticky bottom)
        ══════════════════════════════════════════ */
        .kt-wrapper {
            flex: 1;                /* neemt alle beschikbare ruimte */
            display: flex;
            flex-direction: column;
        }

        .kt-content {
            max-width: 1200px;
            width: 100%;
            margin: 1.75rem auto;
            padding: 0 1rem;
            flex: 1;
        }

        /* Footer altijd onderaan, nooit zwevend */
        .kt-footer {
            background: #f0f0f0;
            text-align: center;
            padding: 1rem;
            font-size: .77rem;
            color: #aaa;
            border-top: 1px solid #e0e0e0;
            margin-top: auto;        /* duwt footer naar beneden als content kort is */
        }

        /* Flash banner */
        .kt-flash-wrap {
            position: sticky;
            top: 0;
            z-index: 98;
            padding: .4rem 1rem 0;
        }
    </style>
</head>
<body>

<?php
$isIngelogd = !empty($_SESSION['gebruiker_id']);

if ($isIngelogd):
    $huidig = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $huidig = rtrim($huidig, '/') ?: '/';
    $base   = defined('BASE_URL') ? BASE_URL : '';
    if ($base && str_starts_with($huidig, $base)) {
        $huidig = substr($huidig, strlen($base));
    }
    $huidig = $huidig ?: '/';

    $navItems = [
        '/dashboard'       => 'Accounts',
        '/medewerkers'     => 'Medewerkers',
        '/beschikbaarheid' => 'Beschikbaarheid',
        '/klanten'         => 'Klanten',
        '/afspraken'       => 'Afspraken',
        '/behandelingen'   => 'Behandelingen',
        '/producten'       => 'Producten',
        '/bestellingen'    => 'Bestellingen',
    ];

    $gebruikerNaam = htmlspecialchars($_SESSION['gebruiker_naam'] ?? '', ENT_QUOTES, 'UTF-8');
    $gebruikerRol  = htmlspecialchars($_SESSION['gebruiker_rol']  ?? '', ENT_QUOTES, 'UTF-8');
?>

<!-- ── Desktop navbar ── -->
<nav class="kt-nav">
    <a class="kt-brand" href="<?= url('/dashboard') ?>">Kniploket Tiko</a>

    <!-- Desktop links -->
    <ul class="kt-links">
        <?php foreach ($navItems as $pad => $label):
            $actief = str_starts_with($huidig, $pad) ? 'actief' : '';
        ?>
        <li><a href="<?= url($pad) ?>" class="<?= $actief ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
    </ul>

    <!-- Gebruiker + uitloggen (desktop) -->
    <div class="kt-right">
        <span class="kt-user"><?= $gebruikerNaam ?> (<?= $gebruikerRol ?>)</span>
        <a href="<?= url('/logout') ?>" class="kt-logout">Uitloggen</a>
    </div>

    <!-- Hamburger (mobiel) -->
    <button class="kt-hamburger" id="kt-hamburger" aria-label="Menu openen" aria-expanded="false">
        <span></span><span></span><span></span>
    </button>
</nav>

<!-- ── Mobile dropdown menu ── -->
<ul class="kt-mobile-menu" id="kt-mobile-menu" role="menu">
    <?php foreach ($navItems as $pad => $label):
        $actief = str_starts_with($huidig, $pad) ? 'actief' : '';
    ?>
    <li><a href="<?= url($pad) ?>" class="<?= $actief ?>"><?= $label ?></a></li>
    <?php endforeach; ?>
    <li class="kt-mobile-divider"></li>
    <li class="kt-mobile-user"><?= $gebruikerNaam ?> (<?= $gebruikerRol ?>)</li>
    <li><a href="<?= url('/logout') ?>">Uitloggen</a></li>
</ul>

<?php endif; ?>

<!-- ── Flash bericht ── -->
<?php if (!empty($flash)): ?>
<div class="kt-flash-wrap">
    <div id="kt-flash-bar" class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-0 shadow-sm" role="alert">
        <?= htmlspecialchars($flash['bericht'], ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sluiten"></button>
    </div>
</div>
<?php endif; ?>

<!-- ── Pagina wrapper (content + footer) ── -->
<div class="kt-wrapper">
    <div class="kt-content">
        <?= $inhoud ?>
    </div>

    <!-- ── Footer altijd onderaan ── -->
    <footer class="kt-footer">
        © 2026 Kniploket Tiko – Alle rechten voorbehouden
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    // ── Flash auto-dismiss na 3 seconden ──
    const flashBar = document.getElementById('kt-flash-bar');
    if (flashBar) {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(flashBar);
            bsAlert.close();
        }, 3000);
    }

    // ── Hamburger menu toggle ──
    const btn  = document.getElementById('kt-hamburger');
    const menu = document.getElementById('kt-mobile-menu');

    if (btn && menu) {
        btn.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('open');
            btn.classList.toggle('open', isOpen);
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        // Sluit menu als je buiten klikt
        document.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        // Sluit menu bij navigatie (link klik)
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.remove('open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            });
        });
    }
})();
</script>
</body>
</html>
