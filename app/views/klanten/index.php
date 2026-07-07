<?php
if (!function_exists('klantUrl')) {
    function klantUrl(int $pagina, string $postcode): string {
        $params = ['pagina' => $pagina];
        if ($postcode !== '') { $params['postcode'] = $postcode; }
        return url('/klanten?' . http_build_query($params));
    }
}
?>
<style>
    body { background: #f0f0f0; }

    /* ── Breadcrumb ── */
    .kl-bc { font-size:.84rem; margin-bottom:.5rem; color:#555; }
    .kl-bc a { color:#555; text-decoration:none; }
    .kl-bc a:hover { text-decoration:underline; }

    /* ── Titel ── */
    .kl-h1 { color:#c0392b; font-size:1.45rem; font-weight:700; margin-bottom:1rem; }

    /* ── Filter kaart ── */
    .kl-filter-card {
        background:#fff;
        border:1px solid #ddd;
        border-radius:.35rem;
        padding:1rem 1.25rem;
        margin-bottom:.75rem;
        display:flex;
        align-items:flex-end;
        justify-content:flex-end;
        gap:.5rem;
        flex-wrap:wrap;
    }
    .kl-filter-grp { display:flex; flex-direction:column; gap:.18rem; }
    .kl-filter-grp label { font-size:.77rem; font-weight:600; color:#333; }
    .kl-input {
        border:1px solid #bbb;
        border-radius:.25rem;
        padding:.36rem .65rem;
        font-size:.84rem;
        color:#333;
        width:210px;
        max-width:100%;
        background:#fff;
    }
    .kl-input:focus { outline:none; border-color:#c0392b; }
    .kl-input::placeholder { color:#aaa; }
    .kl-btn-toon {
        background:#c0392b; color:#fff; border:none;
        border-radius:.25rem; padding:.38rem 1.05rem;
        font-size:.82rem; font-weight:600; cursor:pointer;
        white-space:nowrap; height:32px;
    }
    .kl-btn-toon:hover { background:#a93226; }
    .kl-btn-reset {
        background:#6c757d; color:#fff; border:none;
        border-radius:.25rem; padding:.38rem .85rem;
        font-size:.82rem; font-weight:600; cursor:pointer;
        white-space:nowrap; text-decoration:none;
        display:inline-flex; align-items:center; height:32px;
    }
    .kl-btn-reset:hover { background:#5a6268; color:#fff; }

    /* ── Resultaten kaart ── */
    .kl-result-card {
        background:#fff;
        border:1px solid #ddd;
        border-radius:.35rem;
        overflow:hidden;
    }
    .kl-count-bar {
        padding:.5rem 1rem;
        font-size:.81rem;
        color:#555;
        border-bottom:1px solid #eee;
    }

    /* ── Paginering ── */
    .kl-pagination {
        display:flex; justify-content:center; align-items:center;
        gap:.22rem; padding:.55rem 1rem; border-bottom:1px solid #eee;
        flex-wrap:wrap;
    }
    .kl-pagination a, .kl-pagination span {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:32px; height:32px; padding:0 .4rem;
        border:1px solid #ccc; border-radius:.22rem;
        font-size:.81rem; text-decoration:none; color:#444;
        background:#fff; line-height:1;
    }
    .kl-pagination a:hover { background:#f0f0f0; }
    .kl-pagination .pg-active { background:#c0392b; color:#fff; border-color:#c0392b; font-weight:700; }
    .kl-pagination .pg-disabled { color:#bbb; cursor:default; background:#fafafa; }

    /* ── Tabel wrapper: horizontaal scrollen op kleine schermen ── */
    .kl-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }

    /* ── Tabel ── */
    .kl-table { width:100%; border-collapse:collapse; min-width:600px; }
    .kl-table thead tr { background:#c0392b; }
    .kl-table thead th {
        color:#fff; font-size:.79rem; font-weight:700;
        padding:.5rem .8rem; text-align:left; white-space:nowrap;
    }
    .kl-table tbody tr { border-bottom:1px solid #f0f0f0; }
    .kl-table tbody tr:last-child { border-bottom:none; }
    .kl-table tbody tr:hover { background:#fafafa; }
    .kl-table tbody td { padding:.5rem .8rem; font-size:.82rem; color:#2c3e50; vertical-align:middle; }

    .kl-btn-detail {
        display:inline-block; border:1px solid #007bff;
        background:#fff; color:#333; border-radius:.22rem;
        padding:.22rem .8rem; font-size:.77rem;
        text-decoration:none; white-space:nowrap;
    }
    .kl-btn-detail:hover { background:#007bff; border-color:#007bff; color:#fff; }

    .kl-empty { text-align:center; padding:1.8rem 1rem; color:#666; font-size:.86rem; }

    /* ── Mobile: filter neemt volledige breedte ── */
    @media (max-width: 768px) {
        .kl-filter-card { 
            justify-content:flex-start; 
            padding: 1rem;
        }
        .kl-filter-grp  { width:100%; }
        .kl-input        { width:100%; }
        .kl-btn-toon,
        .kl-btn-reset    { flex:1; text-align:center; justify-content:center; }
        .kl-h1 {
            font-size: 1.25rem;
        }
        .kl-bc {
            font-size: .78rem;
        }
    }

    @media (max-width: 480px) {
        .kl-filter-card {
            padding: 0.75rem;
            gap: 0.4rem;
        }
        .kl-btn-toon,
        .kl-btn-reset {
            padding: .38rem 0.5rem;
            font-size: .78rem;
        }
    }
</style>

<!-- Breadcrumb -->
<div class="kl-bc">
    <a href="<?= url('/dashboard') ?>">Home</a> / Klanten
</div>

<!-- Titel -->
<h1 class="kl-h1">Overzicht klanten</h1>

<!-- Filter kaart -->
<form method="get" action="<?= url('/klanten') ?>">
    <div class="kl-filter-card">
        <div class="kl-filter-grp">
            <label for="postcode">Postcode zoeken</label>
            <input
                type="text"
                id="postcode"
                name="postcode"
                class="kl-input"
                placeholder="Bijv. 3512AB"
                value="<?= htmlspecialchars($gezochtPostcode, ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>
        <button type="submit" class="kl-btn-toon">Toon klanten</button>
        <a href="<?= url('/klanten') ?>" class="kl-btn-reset">Reset</a>
    </div>
</form>

<!-- Resultaten kaart -->
<div class="kl-result-card">

    <!-- Teller -->
    <div class="kl-count-bar">
        Gevonden klanten – <?= $totaalKlanten ?> klant(en)
    </div>

    <!-- Paginering -->
    <?php if ($totaalPaginas > 1): ?>
    <div class="kl-pagination">
        <?php if ($huidigePagina > 1): ?>
            <a href="<?= klantUrl($huidigePagina - 1, $gezochtPostcode) ?>">&#8249;</a>
        <?php else: ?>
            <span class="pg-disabled">&#8249;</span>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $totaalPaginas; $p++): ?>
            <?php if ($p === $huidigePagina): ?>
                <span class="pg-active"><?= $p ?></span>
            <?php else: ?>
                <a href="<?= klantUrl($p, $gezochtPostcode) ?>"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($huidigePagina < $totaalPaginas): ?>
            <a href="<?= klantUrl($huidigePagina + 1, $gezochtPostcode) ?>">&#8250;</a>
        <?php else: ?>
            <span class="pg-disabled">&#8250;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Tabel -->
    <div class="kl-table-wrap">
    <table class="kl-table">
        <thead>
            <tr>
                <th>Naam</th>
                <th>Relatienummer</th>
                <th>Adres</th>
                <th>Postcode</th>
                <th>Woonplaats</th>
                <th>Mobiel</th>
                <th>Contact e-mail</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($klanten)): ?>
                <tr>
                    <td colspan="8" class="kl-empty">
                        <?php if ($filterActief && $totaalKlanten === 0): ?>
                            Er zijn geen klanten bekent die de geselecteerde postcode hebben
                        <?php else: ?>
                            Geen klanten gevonden.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($klanten as $k):
                    $naam  = $k['Voornaam']
                           . ($k['Tussenvoegsel'] ? ' ' . $k['Tussenvoegsel'] : '')
                           . ' ' . $k['Achternaam'];
                    $adres = trim(
                        ($k['Straatnaam'] ?? '')
                        . ' ' . ($k['Huisnummer'] ?? '')
                        . ($k['Toevoeging'] ?? '')
                    );
                ?>
                <tr>
                    <td><?= htmlspecialchars($naam,                     ENT_QUOTES,'UTF-8') ?></td>
                    <td><?= htmlspecialchars($k['Relatienummer'],        ENT_QUOTES,'UTF-8') ?></td>
                    <td><?= htmlspecialchars($adres,                     ENT_QUOTES,'UTF-8') ?></td>
                    <td><?= htmlspecialchars($k['Postcode']   ?? '',     ENT_QUOTES,'UTF-8') ?></td>
                    <td><?= htmlspecialchars($k['Plaats']     ?? '',     ENT_QUOTES,'UTF-8') ?></td>
                    <td><?= htmlspecialchars($k['Mobiel']     ?? '',     ENT_QUOTES,'UTF-8') ?></td>
                    <td><?= htmlspecialchars($k['Email']      ?? '',     ENT_QUOTES,'UTF-8') ?></td>
                    <td>
                        <a href="<?= url('/klanten/detail?id=' . (int)$k['Id']) ?>" class="kl-btn-detail">Details</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </div>

</div>


