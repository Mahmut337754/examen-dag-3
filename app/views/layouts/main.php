<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kniploket Tiko</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f2f2f2; font-family: 'Segoe UI', sans-serif; margin: 0; }

        /* ── Navbar ── */
        .kt-nav {
            background: #c0392b;
            display: flex;
            align-items: stretch;
            min-height: 48px;
            padding: 0 1rem;
        }
        .kt-nav .kt-brand {
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
        }
        .kt-nav .kt-links {
            display: flex;
            align-items: stretch;
            list-style: none;
            margin: 0;
            padding: 0;
            flex: 1;
        }
        .kt-nav .kt-links li a {
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
        .kt-nav .kt-links li a:hover {
            background: rgba(0,0,0,.15);
            color: #fff;
        }
        .kt-nav .kt-links li a.actief {
            background: rgba(0,0,0,.18);
            border-bottom-color: #fff;
            color: #fff;
        }
        .kt-nav .kt-right {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-left: auto;
        }
        .kt-nav .kt-user {
            color: rgba(255,255,255,.85);
            font-size: .8rem;
            white-space: nowrap;
        }
        .kt-nav .kt-logout {
            background: rgba(0,0,0,.22);
            color: #fff;
            border: 1px solid rgba(255,255,255,.35);
            border-radius: .25rem;
            font-size: .78rem;
            padding: .2rem .75rem;
            text-decoration: none;
            white-space: nowrap;
        }
        .kt-nav .kt-logout:hover {
            background: rgba(0,0,0,.38);
            color: #fff;
        }

        /* ── Inhoud ── */
        .kt-content {
            max-width: 1200px;
            margin: 1.75rem auto;
            padding: 0 1rem;
        }
    </style>
</head>
<body>

<?php if (!empty($_SESSION['gebruiker_id'])):
    // Bepaal actieve route voor markering
    $huidig = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $huidig = rtrim($huidig, '/') ?: '/';
    $base   = defined('BASE_URL') ? BASE_URL : '';
    if ($base && str_starts_with($huidig, $base)) {
        $huidig = substr($huidig, strlen($base));
    }
    $huidig = $huidig ?: '/';
?>
<nav class="kt-nav">
    <a class="kt-brand" href="<?= url('/dashboard') ?>">Kniploket Tiko</a>

    <ul class="kt-links">
        <?php
        $items = [
            '/dashboard'       => 'Accounts',
            '/medewerkers'     => 'Medewerkers',
            '/beschikbaarheid' => 'Beschikbaarheid',
            '/klanten'         => 'Klanten',
            '/afspraken'       => 'Afspraken',
            '/behandelingen'   => 'Behandelingen',
            '/producten'       => 'Producten',
            '/bestellingen'    => 'Bestellingen',
        ];
        foreach ($items as $pad => $label):
            $actief = str_starts_with($huidig, $pad) ? 'actief' : '';
        ?>
        <li><a href="<?= url($pad) ?>" class="<?= $actief ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="kt-right">
        <span class="kt-user">
            <?= htmlspecialchars($_SESSION['gebruiker_naam'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            (<?= htmlspecialchars($_SESSION['gebruiker_rol'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
        </span>
        <a href="<?= url('/logout') ?>" class="kt-logout">Uitloggen</a>
    </div>
</nav>
<?php endif; ?>

<?php if (!empty($flash)): ?>
<div style="position:fixed;top:52px;left:0;right:0;z-index:999;padding:.5rem 1rem;">
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-0 shadow-sm">
        <?= htmlspecialchars($flash['bericht'], ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<div style="height:56px;"></div>
<?php endif; ?>

<div class="kt-content">
    <?= $inhoud ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
