<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= $base ?>/producten" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">
                <i class="bi bi-box-seam me-2 text-primary"></i>
                <?= htmlspecialchars($product['productnaam'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </h2>
        </div>

        <!-- Productgegevens -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-info-circle me-2"></i>Productgegevens
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Productnaam</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($product['productnaam'] ?? '–', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-4">Categorie</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($product['categorie'] ?? '–', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-4">EAN-code</dt>
                    <dd class="col-sm-8" style="font-family: monospace;">
                        <?= htmlspecialchars($product['ean_code'] ?? '–', ENT_QUOTES, 'UTF-8') ?>
                    </dd>

                    <dt class="col-sm-4">Voorraad</dt>
                    <dd class="col-sm-8">
                        <?php if ($product['voorraad'] == 0): ?>
                            <span class="badge bg-danger">Uitverkocht</span>
                        <?php elseif ($product['voorraad'] < 5): ?>
                            <span class="badge bg-warning">Laag (<?= (int)$product['voorraad'] ?> stuks)</span>
                        <?php else: ?>
                            <span class="badge bg-success"><?= (int)$product['voorraad'] ?> stuks</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-4">Leverancier</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($product['leverancier_naam'] ?? '–', ENT_QUOTES, 'UTF-8') ?></dd>

                    <dt class="col-sm-4">Prijs</dt>
                    <dd class="col-sm-8 fw-semibold">€<?= number_format((float)$product['prijs'], 2, ',', '.') ?></dd>
                </dl>
            </div>
        </div>

    </div>
</div>