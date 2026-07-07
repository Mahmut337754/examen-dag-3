<?php
$rol      = htmlspecialchars(ucfirst($_SESSION['gebruiker_rol']  ?? ''), ENT_QUOTES, 'UTF-8');
$naam     = htmlspecialchars($_SESSION['gebruiker_naam'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<style>
    body { background:#f0f0f0; }

    .db-badge {
        display:inline-block;
        background:#e8b800; color:#fff;
        font-size:.72rem; font-weight:700;
        padding:.18rem .65rem; border-radius:.25rem;
        letter-spacing:.5px; text-transform:uppercase;
        margin-bottom:.6rem;
    }
    .db-h1 {
        font-size:1.75rem; font-weight:700;
        color:#1a1a1a; margin-bottom:.15rem;
    }
    .db-sub {
        font-size:.88rem; color:#555; margin-bottom:1.5rem;
    }
    .db-breadcrumb {
        font-size:.82rem; color:#888; margin-bottom:1.4rem;
    }

    /* Kaarten grid */
    .db-grid {
        display:grid;
        grid-template-columns:repeat(4, 1fr);
        gap:1rem;
    }
    @media (max-width:900px)  { .db-grid { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:480px)  { .db-grid { grid-template-columns:1fr; } }

    .db-card {
        background:#fff;
        border:1px solid #e0e0e0;
        border-radius:.4rem;
        padding:1.1rem 1.1rem 1rem;
        display:flex;
        flex-direction:column;
        gap:.4rem;
    }
    .db-card-title {
        font-size:.95rem; font-weight:700; color:#1a1a1a;
    }
    .db-card-desc {
        font-size:.8rem; color:#666; line-height:1.4;
        flex:1;
    }
    .db-btn-open {
        display:inline-block;
        border:1px solid #ced4da;
        background:#fff; color:#333;
        border-radius:.22rem;
        padding:.22rem .75rem;
        font-size:.79rem;
        text-decoration:none;
        width:fit-content;
        margin-top:.25rem;
        transition:background .1s;
    }
    .db-btn-open:hover { background:#f0f0f0; color:#333; }

    /* Mobile: titel iets kleiner */
    @media (max-width:480px) {
        .db-h1 { font-size:1.45rem; }
        .db-sub { font-size:.83rem; }
    }
</style>

<!-- Badge + Titel -->
<div class="db-badge">Kapsalon applicatie</div>
<h1 class="db-h1"><?= $rol ?></h1>
<div class="db-breadcrumb">Home</div>
<p class="db-sub">Welkom bij Kniploket Tiko – hier regel je eenvoudig klanten, afspraken en planning voor de salon.</p>

<!-- Kaarten -->
<div class="db-grid">

    <div class="db-card">
        <div class="db-card-title">Accounts</div>
        <div class="db-card-desc">Beheer gebruikersaccounts en roltoewijzingen.</div>
        <a href="<?= url('/dashboard') ?>" class="db-btn-open">Openen</a>
    </div>

    <div class="db-card">
        <div class="db-card-title">Medewerkers</div>
        <div class="db-card-desc">Overzicht van medewerkers en hun basisgegevens.</div>
        <a href="<?= url('/medewerkers') ?>" class="db-btn-open">Openen</a>
    </div>

    <div class="db-card">
        <div class="db-card-title">Beschikbaarheid</div>
        <div class="db-card-desc">Bekijk de beschikbaarheid van medewerkers per dag en tijd.</div>
        <a href="<?= url('/beschikbaarheid') ?>" class="db-btn-open">Openen</a>
    </div>

    <div class="db-card">
        <div class="db-card-title">Klanten</div>
        <div class="db-card-desc">Bekijk en filter klantgegevens op postcode en contactinformatie.</div>
        <a href="<?= url('/klanten') ?>" class="db-btn-open">Openen</a>
    </div>

    <div class="db-card">
        <div class="db-card-title">Afspraken</div>
        <div class="db-card-desc">Plan, bekijk en beheer afspraken met status en tijd.</div>
        <a href="<?= url('/afspraken') ?>" class="db-btn-open">Openen</a>
    </div>

    <div class="db-card">
        <div class="db-card-title">Behandelingen</div>
        <div class="db-card-desc">Overzicht van behandelingen, duur en prijsinformatie.</div>
        <a href="<?= url('/behandelingen') ?>" class="db-btn-open">Openen</a>
    </div>

    <div class="db-card">
        <div class="db-card-title">Producten</div>
        <div class="db-card-desc">Bekijk en beheer producten binnen het assortiment.</div>
        <a href="<?= url('/producten') ?>" class="db-btn-open">Openen</a>
    </div>

    <div class="db-card">
        <div class="db-card-title">Bestellingen</div>
        <div class="db-card-desc">Bekijk en beheer klantbestellingen en bestelstatus.</div>
        <a href="<?= url('/bestellingen') ?>" class="db-btn-open">Openen</a>
    </div>

</div>


