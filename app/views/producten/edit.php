<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= $base ?>/producten" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                Product wijzigen: <?= htmlspecialchars($product['productnaam'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </h2>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?= url('/producten/wijzigen') ?>" novalidate id="productForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">

                    <div class="row g-3">

                        <!-- Productnaam -->
                        <div class="col-md-6">
                            <label for="productnaam" class="form-label fw-semibold">
                                Productnaam <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="productnaam" name="productnaam"
                                required minlength="2" maxlength="150"
                                value="<?= htmlspecialchars($formData['productnaam'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Productnaam is verplicht (min. 2 tekens).
                            </div>
                        </div>

                        <!-- Categorie -->
                        <div class="col-md-6">
                            <label for="categorie" class="form-label fw-semibold">
                                Categorie <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="categorie" name="categorie"
                                required maxlength="100"
                                value="<?= htmlspecialchars($formData['categorie'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Categorie is verplicht (max. 100 tekens).
                            </div>
                        </div>

                        <!-- EAN-code -->
                        <div class="col-md-6">
                            <label for="ean_code" class="form-label fw-semibold">
                                EAN-code <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="ean_code" name="ean_code"
                                required pattern="[0-9]{13}" maxlength="13"
                                value="<?= htmlspecialchars($formData['ean_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="13 cijfers">
                            <div class="invalid-feedback">
                                EAN-code moet exact 13 cijfers bevatten.
                            </div>
                        </div>

                        <!-- Voorraad -->
                        <div class="col-md-6">
                            <label for="voorraad" class="form-label fw-semibold">
                                Voorraad <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="voorraad" name="voorraad"
                                required min="0" step="1"
                                value="<?= htmlspecialchars($formData['voorraad'] ?? 0, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Voorraad moet een positief getal zijn.
                            </div>
                        </div>

                        <!-- Leverancier -->
                        <div class="col-md-6">
                            <label for="leverancier_id" class="form-label fw-semibold">
                                Leverancier <span class="text-danger">*</span>
                            </label>
                            <select id="leverancier_id" name="leverancier_id" class="form-select" required>
                                <option value="">Selecteer een leverancier</option>
                                <?php foreach ($alleLeveranciers as $leverancier): ?>
                                    <option value="<?= (int)$leverancier['id'] ?>"
                                        <?= (isset($formData['leverancier_id']) && (int)$formData['leverancier_id'] === (int)$leverancier['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($leverancier['naam'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Selecteer een leverancier.
                            </div>
                        </div>

                        <!-- Prijs -->
                        <div class="col-md-6">
                            <label for="prijs" class="form-label fw-semibold">
                                Prijs (€) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="prijs" name="prijs"
                                required min="0" step="0.01"
                                value="<?= htmlspecialchars($formData['prijs'] ?? 0, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Prijs moet een positief getal zijn.
                            </div>
                        </div>

                    </div><!-- /row -->

                    <hr class="my-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Wijzigingen opslaan
                        </button>
                        <a href="<?= $base ?>/producten/detail?id=<?= (int)$product['id'] ?>"
                           class="btn btn-outline-secondary">Annuleren</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Formuliervalidatie
(function () {
    'use strict';
    var form = document.getElementById('productForm');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
})();
</script>