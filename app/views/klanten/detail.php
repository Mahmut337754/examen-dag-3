<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= $base ?>/klanten" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">
                <i class="bi bi-person-circle me-2 text-primary"></i>
                <?= htmlspecialchars($klant['naam'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </h2>
        </div>

        <!-- Klantgegevens -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person-vcard me-2"></i>Persoonlijke gegevens
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Naam</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($klant['naam'] ?? '–', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-4">E-mailadres</dt>
                    <dd class="col-sm-8">
                        <a href="mailto:<?= htmlspecialchars($klant['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($klant['email'] ?? '–', ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </dd>

                    <dt class="col-sm-4">Telefoonnummer</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($klant['telefoonnummer'] ?? '–', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-4">Adres</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($klant['adres'] ?? '–', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        <?php if ($klant['is_actief']): ?>
                            <span class="badge bg-success">Actief</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactief</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Allergieën & wensen -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <i class="bi bi-clipboard2-heart me-2 text-danger"></i>Allergieën &amp; wensen
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Allergieën</dt>
                    <dd class="col-sm-8">
                        <?php if (!empty($allergenen)): ?>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($allergenen as $naam): ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <?= htmlspecialchars($naam, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted">Geen bekende allergieën</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-4">Wensen</dt>
                    <dd class="col-sm-8">
                        <?php if (!empty($klant['wensen'])): ?>
                            <?= htmlspecialchars($klant['wensen'], ENT_QUOTES, 'UTF-8') ?>
                        <?php else: ?>
                            <span class="text-muted">Geen wensen opgegeven</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>

    </div>
</div>

