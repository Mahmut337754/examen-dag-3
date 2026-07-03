<a href="<?= url('/klanten') ?>" class="btn btn-outline-secondary mb-4">
    <i class="bi bi-arrow-left me-1"></i>Terug naar klanten
</a>

<h1 class="mb-4">
    <i class="bi bi-person me-2 text-primary"></i>
    <?= htmlspecialchars($klant['Voornaam'] . ' ' . $klant['Achternaam'], ENT_QUOTES, 'UTF-8') ?>
</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Relatienummer</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($klant['Relatienummer'], ENT_QUOTES, 'UTF-8') ?></dd>

            <dt class="col-sm-3">Voornaam</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($klant['Voornaam'], ENT_QUOTES, 'UTF-8') ?></dd>

            <dt class="col-sm-3">Tussenvoegsel</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($klant['Tussenvoegsel'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>

            <dt class="col-sm-3">Achternaam</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($klant['Achternaam'], ENT_QUOTES, 'UTF-8') ?></dd>

            <dt class="col-sm-3">E-mail</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($klant['email'], ENT_QUOTES, 'UTF-8') ?></dd>

            <dt class="col-sm-3">Bijzonderheden</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($klant['Bijzonderheden'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
        </dl>
    </div>
</div>
