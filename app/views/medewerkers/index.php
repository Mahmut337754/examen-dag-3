<?php
if (!function_exists('medewerkerUrl')) {
    function medewerkerUrl(int $pagina, string $specialisatie): string {
        $params = ['pagina' => $pagina];
        if ($specialisatie !== '' && $specialisatie !== 'Alle specialisaties') {
            $params['specialisatie'] = $specialisatie;
        }
        return url('/medewerkers?' . http_build_query($params));
    }
}
?>
<style>
    /* ── Pagina achtergrond ── */
    body { background: #f0f0f0; }

    /* ── Breadcrumb ── */
    .mw-bc { font-size: .84rem; margin-bottom: .5rem; color: #555; }
    .mw-bc a { color: #555; text-decoration: none; }
    .mw-bc a:hover { text-decoration: underline; }
    .mw-bc span { color: #333; }

    /* ── Paginatitel ── */
    .mw-h1 {
        color: #c0392b;
        font-size: 1.45rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    /* ── Filter kaart ── */
    .mw-filter-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: .35rem;
        padding: 1rem 1.25rem 1rem 1.25rem;
        margin-bottom: .75rem;
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
        gap: .5rem;
        min-height: 76px;
    }

    .mw-filter-grp {
        display: flex;
        flex-direction: column;
        gap: .18rem;
    }
    .mw-filter-grp label {
        font-size: .77rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0;
    }

    /* Dropdown exact zoals wireframe */
    .mw-select-wrap {
        position: relative;
        display: inline-block;
    }
    .mw-select {
        appearance: none;
        -webkit-appearance: none;
        background: #fff;
        border: 1px solid #bbb;
        border-radius: .25rem;
        padding: .36rem 2.2rem .36rem .65rem;
        font-size: .84rem;
        color: #333;
        width: 230px;
        cursor: pointer;
        line-height: 1.4;
    }
    .mw-select:focus { outline: none; border-color: #c0392b; }
    .mw-select-arrow {
        pointer-events: none;
        position: absolute;
        right: .6rem;
        top: 50%;
        transform: translateY(-50%);
        width: 0; height: 0;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 5px solid #666;
    }

    .mw-btn-toon {
        background: #c0392b;
        color: #fff;
        border: none;
        border-radius: .25rem;
        padding: .38rem 1.05rem;
        font-size: .82rem;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        height: 32px;
        align-self: flex-end;
    }
    .mw-btn-toon:hover { background: #a93226; }

    .mw-btn-reset {
        background: #6c757d;
        color: #fff;
        border: none;
        border-radius: .25rem;
        padding: .38rem .85rem;
        font-size: .82rem;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        height: 32px;
        align-self: flex-end;
    }
    .mw-btn-reset:hover { background: #5a6268; color: #fff; }

    /* ── Resultaten kaart ── */
    .mw-result-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: .35rem;
        overflow: hidden;
    }

    /* Teller regel */
    .mw-count-bar {
        padding: .5rem 1rem;
        font-size: .81rem;
        color: #555;
        border-bottom: 1px solid #eee;
    }

    /* ── Paginering ── */
    .mw-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: .22rem;
        padding: .55rem 1rem;
        border-bottom: 1px solid #eee;
    }
    .mw-pagination a,
    .mw-pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 27px;
        height: 27px;
        padding: 0 .35rem;
        border: 1px solid #ccc;
        border-radius: .22rem;
        font-size: .81rem;
        text-decoration: none;
        color: #444;
        background: #fff;
        line-height: 1;
        transition: background .1s;
    }
    .mw-pagination a:hover { background: #f0f0f0; }
    .mw-pagination .pg-active {
        background: #c0392b;
        color: #fff;
        border-color: #c0392b;
        font-weight: 700;
    }
    .mw-pagination .pg-disabled {
        color: #bbb;
        cursor: default;
        background: #fafafa;
    }

    /* ── Tabel ── */
    .mw-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: auto;
    }
    .mw-table thead tr {
        background: #c0392b;
    }
    .mw-table thead th {
        color: #fff;
        font-size: .79rem;
        font-weight: 700;
        padding: .5rem .8rem;
        text-align: left;
        white-space: nowrap;
        border: none;
    }
    .mw-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
    }
    .mw-table tbody tr:last-child {
        border-bottom: none;
    }
    .mw-table tbody tr:hover {
        background: #fafafa;
    }
    .mw-table tbody td {
        padding: .5rem .8rem;
        font-size: .82rem;
        color: #2c3e50;
        vertical-align: middle;
    }

    /* Details knop – exact als wireframe: rand, wit bg */
    .mw-btn-detail {
        display: inline-block;
        border: 1px solid #bbb;
        background: #fff;
        color: #333;
        border-radius: .22rem;
        padding: .17rem .7rem;
        font-size: .77rem;
        text-decoration: none;
        white-space: nowrap;
        transition: background .1s;
    }
    .mw-btn-detail:hover { background: #efefef; color: #333; }

    /* Leeg bericht */
    .mw-empty {
        text-align: center;
        padding: 1.8rem 1rem;
        color: #666;
        font-size: .86rem;
    }

    /* ── Footer ── */
    .mw-footer {
        text-align: center;
        margin-top: 3rem;
        padding-bottom: 1.5rem;
        font-size: .77rem;
        color: #aaa;
    }
</style>

<!-- Breadcrumb -->
<div class="mw-bc">
    <a href="<?= url('/dashboard') ?>">Home</a>
    <span> / Medewerkers</span>
</div>

<!-- Paginatitel -->
<h1 class="mw-h1">Overzicht medewerkers</h1>

<!-- Filter kaart -->
<form method="get" action="<?= url('/medewerkers') ?>">
    <div class="mw-filter-card">
        <div class="mw-filter-grp">
            <label for="specialisatie">Specialisatie</label>
            <div class="mw-select-wrap">
                <select id="specialisatie" name="specialisatie" class="mw-select">
                    <option value="Alle specialisaties"
                        <?= ($gekozenSpecialisatie === '' || $gekozenSpecialisatie === 'Alle specialisaties') ? 'selected' : '' ?>>
                        Alle specialisaties
                    </option>
                    <?php foreach ($specialisaties as $spec): ?>
                        <option value="<?= htmlspecialchars($spec, ENT_QUOTES, 'UTF-8') ?>"
                            <?= $gekozenSpecialisatie === $spec ? 'selected' : '' ?>>
                            <?= htmlspecialchars($spec, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="mw-select-arrow"></span>
            </div>
        </div>
        <button type="submit" class="mw-btn-toon">Toon medewerkers</button>
        <a href="<?= url('/medewerkers') ?>" class="mw-btn-reset">Reset</a>
    </div>
</form>

<!-- Resultaten kaart -->
<div class="mw-result-card">

    <!-- Teller -->
    <div class="mw-count-bar">
        Gevonden medewerkers – <?= $totaalMedewerkers ?> medewerker(s)
    </div>

    <!-- Paginering -->
    <?php if ($totaalPaginas > 1): ?>
    <div class="mw-pagination">
        <?php if ($huidigePagina > 1): ?>
            <a href="<?= medewerkerUrl($huidigePagina - 1, $gekozenSpecialisatie) ?>">&#8249;</a>
        <?php else: ?>
            <span class="pg-disabled">&#8249;</span>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $totaalPaginas; $p++): ?>
            <?php if ($p === $huidigePagina): ?>
                <span class="pg-active"><?= $p ?></span>
            <?php else: ?>
                <a href="<?= medewerkerUrl($p, $gekozenSpecialisatie) ?>"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($huidigePagina < $totaalPaginas): ?>
            <a href="<?= medewerkerUrl($huidigePagina + 1, $gekozenSpecialisatie) ?>">&#8250;</a>
        <?php else: ?>
            <span class="pg-disabled">&#8250;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Tabel -->
    <table class="mw-table">
        <thead>
            <tr>
                <th>Naam</th>
                <th>Specialisatie</th>
                <th>Adres</th>
                <th>Postcode</th>
                <th>Woonplaats</th>
                <th>Mobiel</th>
                <th>Contact e-mail</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($geenResultaten): ?>
                <tr>
                    <td colspan="8" class="mw-empty">
                        Er zijn geen medewerkers bekend met de geselecteerde specialisatie
                    </td>
                </tr>
            <?php elseif (empty($medewerkers)): ?>
                <tr>
                    <td colspan="8" class="mw-empty">Geen medewerkers gevonden.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($medewerkers as $m):
                    $naam  = $m['Voornaam']
                           . ($m['Tussenvoegsel'] ? ' ' . $m['Tussenvoegsel'] : '')
                           . ' ' . $m['Achternaam'];
                    $adres = trim(
                        ($m['Straatnaam']  ?? '')
                        . ' ' . ($m['Huisnummer'] ?? '')
                        . ($m['Toevoeging'] ?? '')
                    );
                ?>
                <tr>
                    <td><?= htmlspecialchars($naam,                      ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['Specialisatie'] ?? '',   ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($adres,                      ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['Postcode']      ?? '',   ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['Plaats']        ?? '',   ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['Mobiel']        ?? '',   ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['ContactEmail']  ?? '',   ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a href="<?= url('/medewerkers/detail?id=' . (int)$m['Id']) ?>" class="mw-btn-detail">Details</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<!-- Footer -->
<div class="mw-footer">
    © 2026 Kniploket Tiko – Alle rechten voorbehouden
</div>
