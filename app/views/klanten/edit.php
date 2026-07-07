<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= $base ?>/klanten" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                Klant wijzigen: <?= htmlspecialchars($klant['naam'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </h2>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?= url('/klanten/wijzigen') ?>" novalidate id="klantForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= (int)$klant['id'] ?>">

                    <div class="row g-3">

                        <!-- Naam -->
                        <div class="col-md-6">
                            <label for="naam" class="form-label fw-semibold">
                                Naam <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="naam" name="naam"
                                required minlength="2" maxlength="100"
                                value="<?= htmlspecialchars($formData['naam'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Naam is verplicht (min. 2 tekens, alleen letters).
                            </div>
                        </div>

                        <!-- E-mail -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                E-mailadres <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                required maxlength="255"
                                value="<?= htmlspecialchars($formData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Voer een geldig e-mailadres in.
                            </div>
                        </div>

                        <!-- Nieuw wachtwoord (optioneel) -->
                        <div class="col-md-6">
                            <label for="wachtwoord" class="form-label fw-semibold">
                                Nieuw wachtwoord
                                <span class="badge bg-secondary fw-normal ms-1">optioneel</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="wachtwoord"
                                    name="wachtwoord" minlength="8" maxlength="72"
                                    autocomplete="new-password"
                                    placeholder="Laat leeg om niet te wijzigen">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="toggleWw('wachtwoord','oog1')">
                                    <i class="bi bi-eye" id="oog1"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Laat leeg om huidig wachtwoord te bewaren.
                                Nieuw wachtwoord: min. 8 tekens, 1 hoofdletter, 1 kleine letter, 1 cijfer.
                            </div>
                            <div class="invalid-feedback" id="wwFout">
                                Wachtwoord voldoet niet aan de eisen.
                            </div>
                            <div class="progress mt-1" style="height:4px;" id="wwProgress" hidden>
                                <div class="progress-bar" id="wwBar" role="progressbar"></div>
                            </div>
                            <small id="wwSterkte" class="text-muted"></small>
                        </div>

                        <!-- Telefoonnummer -->
                        <div class="col-md-6">
                            <label for="telefoonnummer" class="form-label fw-semibold">Telefoonnummer</label>
                            <input type="tel" class="form-control" id="telefoonnummer"
                                name="telefoonnummer" maxlength="20"
                                pattern="^(\+?[0-9][\d\s\-\.\(\)]{6,18})$"
                                placeholder="bijv. 0612345678 of 020-1234567"
                                value="<?= htmlspecialchars($formData['telefoonnummer'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Voer een geldig telefoonnummer in (bijv. 0612345678, 020-1234567 of +31612345678).
                            </div>
                        </div>

                        <!-- Adres -->
                        <div class="col-12">
                            <label for="adres" class="form-label fw-semibold">Adres</label>
                            <input type="text" class="form-control" id="adres" name="adres"
                                maxlength="255"
                                value="<?= htmlspecialchars($formData['adres'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">Adres mag maximaal 255 tekens bevatten.</div>
                        </div>

                        <!-- Allergenen (searchable multi-select) -->
                        <div class="col-12">
                            <label for="allergenen" class="form-label fw-semibold">
                                <i class="bi bi-exclamation-triangle text-danger me-1"></i>Allergenen
                            </label>
                            <select id="allergenen" name="allergenen[]" multiple
                                class="form-select" data-choices>
                                <?php foreach ($alleAllergenen as $al): ?>
                                    <option value="<?= (int)$al['id'] ?>"
                                        <?= in_array((int)$al['id'], array_map('intval', $geselecteerd)) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($al['naam'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Typ om te zoeken. Meerdere allergenen selecteerbaar.
                            </div>
                        </div>

                        <!-- Wensen -->
                        <div class="col-12">
                            <label for="wensen" class="form-label fw-semibold">Wensen</label>
                            <textarea class="form-control" id="wensen" name="wensen"
                                rows="3" maxlength="1000"
                            ><?= htmlspecialchars($formData['wensen'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                            <div class="form-text" id="wensenTeller">0 / 1000 tekens</div>
                        </div>

                    </div><!-- /row -->

                    <hr class="my-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Wijzigingen opslaan
                        </button>
                        <a href="<?= $base ?>/klanten/detail?id=<?= (int)$klant['id'] ?>"
                           class="btn btn-outline-secondary">Annuleren</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
<script>
new Choices('#allergenen', {
    removeItemButton: true,
    searchEnabled: true,
    searchPlaceholderValue: 'Zoek allergeen...',
    itemSelectText: '',
    noResultsText: 'Geen resultaten gevonden',
    noChoicesText: 'Alle allergenen geselecteerd',
    placeholder: true,
    placeholderValue: 'Selecteer allergenen...',
});

function toggleWw(veldId, icoonId) {
    const v = document.getElementById(veldId);
    const i = document.getElementById(icoonId);
    v.type = v.type === 'password' ? 'text' : 'password';
    i.classList.toggle('bi-eye');
    i.classList.toggle('bi-eye-slash');
}

document.getElementById('wachtwoord').addEventListener('input', function () {
    const ww  = this.value;
    const bar = document.getElementById('wwBar');
    const txt = document.getElementById('wwSterkte');
    const prg = document.getElementById('wwProgress');
    let score = 0;
    if (ww.length >= 8)          score++;
    if (/[A-Z]/.test(ww))         score++;
    if (/[a-z]/.test(ww))         score++;
    if (/[0-9]/.test(ww))         score++;
    if (/[^A-Za-z0-9]/.test(ww))  score++;
    const labels = ['', 'Zeer zwak', 'Zwak', 'Redelijk', 'Sterk', 'Zeer sterk'];
    const colors = ['', 'danger',    'warning', 'info',     'primary', 'success'];
    prg.hidden = ww.length === 0;
    bar.style.width  = (score * 20) + '%';
    bar.className    = 'progress-bar bg-' + (colors[score] || 'secondary');
    txt.textContent  = ww.length > 0 ? (labels[score] || '') : '';
});

const wensen = document.getElementById('wensen');
const teller = document.getElementById('wensenTeller');
function updateTeller() {
    teller.textContent = wensen.value.length + ' / 1000 tekens';
    teller.className = wensen.value.length > 900 ? 'form-text text-warning' : 'form-text text-muted';
}
wensen.addEventListener('input', updateTeller);
updateTeller();
</script>
