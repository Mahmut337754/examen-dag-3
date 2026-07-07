<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kniploket Tiko – Beheerpaneel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-w:    240px;
            --sidebar-bg:   #0f172a;
            --sidebar-hover:#1e293b;
            --accent:       #6366f1;
            --accent-light: #eef2ff;
            --topbar-h:     60px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
        }

        /* ── Topbar ── */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center;
            padding: 0 1.25rem;
            z-index: 1030;
            gap: 1rem;
        }
        .topbar-brand {
            font-weight: 700; font-size: 1.1rem; color: var(--accent);
            text-decoration: none; white-space: nowrap;
        }
        .topbar-brand i { margin-right: .4rem; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: .75rem; }
        .user-chip {
            display: flex; align-items: center; gap: .5rem;
            padding: .35rem .75rem;
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 2rem; font-size: .85rem;
        }
        .user-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: .75rem;
        }

        /* ── Hamburger toggle ── */
        .sidebar-toggle {
            background: none; border: none; padding: .4rem;
            cursor: pointer; color: #64748b;
            border-radius: .375rem;
            display: none;
        }
        .sidebar-toggle:hover { background: #f1f5f9; }
        @media (max-width: 991px) {
            .sidebar-toggle { display: flex; align-items: center; justify-content: center; }
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: var(--topbar-h); left: 0;
            width: var(--sidebar-w);
            height: calc(100vh - var(--topbar-h));
            background: var(--sidebar-bg);
            overflow-y: auto;
            padding: 1rem .75rem;
            transition: transform .25s ease;
            z-index: 1020;
        }
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
        }
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 1019;
        }
        .sidebar-overlay.show { display: block; }

        .sidebar-section {
            font-size: .7rem; text-transform: uppercase; letter-spacing: .08em;
            color: #475569; font-weight: 600;
            padding: .75rem .75rem .25rem;
            margin-top: .5rem;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: .6rem;
            padding: .6rem .75rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: .5rem;
            font-size: .9rem;
            font-weight: 500;
            transition: background .15s, color .15s;
            margin-bottom: .15rem;
        }
        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #e2e8f0;
        }
        .sidebar-link.active {
            background: var(--accent);
            color: #fff;
        }
        .sidebar-link .badge {
            margin-left: auto;
            font-size: .65rem;
        }

        /* ── Main content ── */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            min-height: calc(100vh - var(--topbar-h));
            padding: 2rem;
            transition: margin-left .25s ease;
        }
        @media (max-width: 991px) {
            .main-content { margin-left: 0; padding: 1.25rem; }
        }

        /* ── Cards ── */
        .stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            padding: 1.25rem 1.5rem;
            transition: box-shadow .2s;
        }
        .stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .stat-icon {
            width: 48px; height: 48px; border-radius: .625rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }

        /* ── Alert override ── */
        .alert { border-radius: .75rem; border: none; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }

        /* ── Page header ── */
        .page-header { margin-bottom: 1.5rem; }
        .page-header h2 { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0; }
        .page-header p  { color: #64748b; margin: .15rem 0 0; font-size: .9rem; }

        /* ── Card ── */
        .card { border: 1px solid #e2e8f0; border-radius: .75rem; }
        .card-header { border-radius: .75rem .75rem 0 0 !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmKi69h56ECk3jM28efF/xWv1os0X"
            crossorigin="anonymous"></script>
</head>
<body>

<!-- Overlay voor mobiele sidebar -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Topbar -->
<header class="topbar">
    <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu">
        <i class="bi bi-list fs-5"></i>
    </button>
    <a class="topbar-brand" href="<?= $base ?>/dashboard">
        <i class="bi bi-scissors"></i>Kniploket Tiko
    </a>
    <div class="topbar-right">
        <?php if (!empty($_SESSION['gebruiker_naam'])): ?>
        <div class="user-chip d-none d-sm-flex">
            <div class="user-avatar">
                <?= strtoupper(mb_substr($_SESSION['gebruiker_naam'], 0, 1)) ?>
            </div>
            <span class="fw-medium text-dark">
                <?= htmlspecialchars($_SESSION['gebruiker_naam'], ENT_QUOTES, 'UTF-8') ?>
            </span>
            <span class="badge bg-light text-secondary border">
                <?= htmlspecialchars($_SESSION['rol'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
        <a href="<?= $base ?>/logout" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-box-arrow-right me-1"></i>
            <span class="d-none d-sm-inline">Uitloggen</span>
        </a>
        <?php endif; ?>
    </div>
</header>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <?php
    $huidigePad = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $isActief = fn(string $deel): string =>
        str_contains($huidigePad, $deel) ? 'active' : '';
    ?>

    <div class="sidebar-section">Navigatie</div>

    <a href="<?= $base ?>/dashboard" class="sidebar-link <?= $isActief('/dashboard') ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="<?= $base ?>/klanten" class="sidebar-link <?= $isActief('/klanten') ?>">
        <i class="bi bi-people"></i> Klanten
    </a>
    <a href="<?= $base ?>/producten" class="sidebar-link <?= $isActief('/producten') ?>">
        <i class="bi bi-box-seam"></i> Producten
    </a>

    <div class="sidebar-section">Account</div>

    <a href="<?= $base ?>/wachtwoord-wijzigen" class="sidebar-link <?= $isActief('/wachtwoord') ?>">
        <i class="bi bi-key"></i> Wachtwoord
    </a>
    <a href="<?= $base ?>/logout" class="sidebar-link" style="color:#f87171;">
        <i class="bi bi-box-arrow-right"></i> Uitloggen
    </a>
</nav>

<!-- Hoofdinhoud -->
<main class="main-content">

    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> fs-5"></i>
            <div><?= $flash['bericht'] ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?= $inhoud ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmKi69h56ECk3jM28efF/xWv1os0X"
        crossorigin="anonymous"></script>
<script src="<?= $base ?>/js/validatie.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
// Sluit sidebar bij resize naar desktop
window.addEventListener('resize', function () {
    if (window.innerWidth > 991) {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }
});
</script>
</body>
</html>
