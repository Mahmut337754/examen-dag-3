<?php
$naam = htmlspecialchars(
    $medewerker['Voornaam']
    . ($medewerker['Tussenvoegsel'] ? ' ' . $medewerker['Tussenvoegsel'] : '')
    . ' ' . $medewerker['Achternaam'],
    ENT_QUOTES, 'UTF-8'
);
?>
<style>
    .md-bc { font-size:.85rem; margin-bottom:.6rem; }
    .md-bc a { color:#c0392b; text-decoration:none; }
    .md-bc a:hover { text-decoration:underline; }

    .md-h1 { font-size:1.55rem; font-weight:700; margin-bottom:1.25rem; }
    .md-h1 span { color:#c0392b; }

    .md-card {
        background:#fff;
        border:1px solid #e0e0e0;
        border-radius:.4rem;
        overflow:hidden;
        max-width:700px;
    }
    .md-row {
        display:grid;
        grid-template-columns:190px 1fr;
        border-bottom:1px solid #f0f0f0;
        font-size:.875rem;
    }
    .md-row:last-child { border-bottom:none; }
    .md-lbl {
        padding:.6rem 1rem;
        font-weight:600;
        color:#333;
        background:#fafafa;
    }
    .md-val {
        padding:.6rem 1rem;
        color:#2c3e50;
    }

    .md-actions {
        display:flex;
        justify-content:flex-end;
        gap:.5rem;
        padding:.9rem 1rem;
        border-top:1px solid #e0e0e0;
        max-width:700px;
    }
    .md-btn-wij {
        background:#c0392b; color:#fff; border:none;
        border-radius:.25rem; padding:.42rem 1.2rem;
        font-size:.85rem; font-weight:600; cursor:pointer;
        text-decoration:none; display:inline-block;
    }
    .md-btn-wij:hover { background:#a93226; color:#fff; }

    .md-btn-terug {
        background:#fff; color:#333;
        border:1px solid #ced4da;
        border-radius:.25rem; padding:.42rem 1rem;
        font-size:.85rem; font-weight:600;
        text-decoration:none; display:inline-block;
    }
    .md-btn-terug:hover { background:#f0f0f0; color:#333; }
</style>

<!-- Breadcrumb -->
<div class="md-bc">
    <a href="<?= url('/dashboard') ?>">Home</a> /
    <a href="<?= url('/medewerkers') ?>">Medewerkers</a> /
    Detail
</div>

<!-- Titel -->
<h1 class="md-h1"><span>Medewerker detail</span> <?= $naam ?></h1>

<!-- Detailkaart -->
<div class="md-card">
    <div class="md-row">
        <div class="md-lbl">Naam</div>
        <div class="md-val"><?= $naam ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Specialisatie</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['Specialisatie'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Geboortedatum</div>
        <div class="md-val">
            <?php
                $geb = $medewerker['Geboortedatum'] ?? null;
                echo $geb ? htmlspecialchars(date('d-m-Y', strtotime($geb)), ENT_QUOTES, 'UTF-8') : '-';
            ?>
        </div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Contact e-mail</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['ContactEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Account e-mail</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['AccountEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Straatnaam</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['Straatnaam'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Huisnummer</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['Huisnummer'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Toevoeging</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['Toevoeging'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Postcode</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['Postcode'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Plaats</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['Plaats'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Mobiel</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['Mobiel'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div class="md-row">
        <div class="md-lbl">Opmerking</div>
        <div class="md-val"><?= htmlspecialchars($medewerker['Opmerking'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
    </div>
</div>

<!-- Knoppen -->
<div class="md-actions">
    <a href="<?= url('/medewerkers/wijzigen?id=' . (int)$medewerker['Id']) ?>" class="md-btn-wij">Wijzigen</a>
    <a href="<?= url('/medewerkers') ?>" class="md-btn-terug">Terug</a>
</div>

