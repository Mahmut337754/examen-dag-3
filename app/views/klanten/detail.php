<?php
$naam = htmlspecialchars(
    $klant['Voornaam']
    . ($klant['Tussenvoegsel'] ? ' ' . $klant['Tussenvoegsel'] : '')
    . ' ' . $klant['Achternaam'],
    ENT_QUOTES, 'UTF-8'
);
?>
<style>
    body { background:#f0f0f0; }
    .kd-bc { font-size:.84rem; margin-bottom:.5rem; color:#555; }
    .kd-bc a { color:#555; text-decoration:none; }
    .kd-bc a:hover { text-decoration:underline; }

    .kd-h1 { font-size:1.45rem; font-weight:700; margin-bottom:1.25rem; }
    .kd-h1 span { color:#c0392b; }

    .kd-card {
        background:#fff; border:1px solid #ddd;
        border-radius:.35rem; overflow:hidden;
        max-width:600px; width:100%;
    }
    .kd-row {
        display:grid; grid-template-columns:160px 1fr;
        border-bottom:1px solid #f0f0f0; font-size:.875rem;
    }
    .kd-row:last-child { border-bottom:none; }
    .kd-lbl { padding:.58rem 1rem; font-weight:600; color:#333; }
    .kd-val { padding:.58rem 1rem; color:#2c3e50; word-break:break-word; }

    .kd-actions {
        display:flex; justify-content:flex-end;
        gap:.5rem; padding:.85rem 0;
        max-width:600px; flex-wrap:wrap;
    }
    .kd-btn-wij {
        background:#c0392b; color:#fff; border:none;
        border-radius:.25rem; padding:.42rem 1.2rem;
        font-size:.84rem; font-weight:600; cursor:pointer;
        text-decoration:none; display:inline-block;
    }
    .kd-btn-wij:hover { background:#a93226; color:#fff; }
    .kd-btn-terug {
        background:#fff; color:#333; border:1px solid #ced4da;
        border-radius:.25rem; padding:.42rem 1rem;
        font-size:.84rem; font-weight:600;
        text-decoration:none; display:inline-block;
    }
    .kd-btn-terug:hover { background:#f0f0f0; color:#333; }

    /* Mobile: label boven waarde ipv naast */
    @media (max-width: 480px) {
        .kd-row { grid-template-columns:1fr; }
        .kd-lbl { padding:.5rem 1rem .1rem; }
        .kd-val { padding:.1rem 1rem .5rem; }
        .kd-actions { justify-content:stretch; }
        .kd-btn-wij, .kd-btn-terug { flex:1; text-align:center; }
    }
</style>

<!-- Breadcrumb -->
<div class="kd-bc">
    <a href="<?= url('/dashboard') ?>">Home</a> /
    <a href="<?= url('/klanten') ?>">Klanten</a> / Detail
</div>

<!-- Titel -->
<h1 class="kd-h1"><span>Klantdetail</span> <?= $naam ?></h1>

<!-- Kaart -->
<div class="kd-card">
    <div class="kd-row">
        <div class="kd-lbl">Naam</div>
        <div class="kd-val"><?= $naam ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Relatienummer</div>
        <div class="kd-val"><?= htmlspecialchars($klant['Relatienummer'], ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Contact e-mail</div>
        <div class="kd-val"><?= htmlspecialchars($klant['Email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Account e-mail</div>
        <div class="kd-val"><?= htmlspecialchars($klant['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Straatnaam</div>
        <div class="kd-val"><?= htmlspecialchars($klant['Straatnaam'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Huisnummer</div>
        <div class="kd-val"><?= htmlspecialchars($klant['Huisnummer'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Toevoeging</div>
        <div class="kd-val"><?= htmlspecialchars($klant['Toevoeging'] ?: '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Postcode</div>
        <div class="kd-val"><?= htmlspecialchars($klant['Postcode'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Plaats</div>
        <div class="kd-val"><?= htmlspecialchars($klant['Plaats'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Mobiel</div>
        <div class="kd-val"><?= htmlspecialchars($klant['Mobiel'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="kd-row">
        <div class="kd-lbl">Bijzonderheden</div>
        <div class="kd-val"><?= htmlspecialchars($klant['Bijzonderheden'] ?: '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
</div>

<!-- Knoppen -->
<div class="kd-actions">
    <a href="<?= url('/klanten/wijzigen?id=' . (int)$klant['Id']) ?>" class="kd-btn-wij">Wijzigen</a>
    <a href="<?= url('/klanten') ?>" class="kd-btn-terug">Terug</a>
</div>


