<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">

<div class="row justify-content-center mt-4">
    <div class="col-lg-7 col-md-9">

        <div class="text-center mb-4">
            <i class="bi bi-scissors fs-1 text-primary"></i>
            <h3 class="mt-2 fw-bold">Kniploket Tiko</h3>
            <p class="text-muted">Maak een account aan</p>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?= url('/registreren') ?>" novalidate id="regForm">
                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="row g-3">

                        <!-- Naam -->
                        <div class="col-md-6">
                            <label for="naam" class="form-label fw-semibold">
                                Naam <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="naam" name="naam"
                                required minlength="2" maxlength="100"
                                autocomplete="name"
                                value="<?= htmlspecialchars($oud['naam'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Naam is verplicht (min. 2 tekens).
                            </div>
                        </div>

                        <!-- E-mail -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                E-mailadres <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                required maxlength="255"
                                autocomplete="email"
                                value="<?= htmlspecialchars($oud['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Voer een geldig e-mailadres in.
                            </div>
                        </div>

                        <!-- Wachtwoord -->
                        <div class="col-md-6">
                            <label for="wachtwoord" class="form-label fw-semibold">
                                Wachtwoord <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="wachtwoord"
                                    name="wachtwoord" required minlength="8" maxlength="72"
                                    autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="toggleWw('wachtwoord','oog1')">
                                    <i class="bi bi-eye" id="oog1"></i>
                                </button>
                            </div>
                            <div class="form-text">Min. 8 tekens, 1 hoofdletter, 1 kleine letter, 1 cijfer.</div>
                            <div class="invalid-feedback">Wachtwoord voldoet niet aan de eisen.</div>
                            <!-- Sterkte-indicator -->
                            <div class="progress mt-1" style="height:4px;" id="wwProgress" hidden>
                                <div class="progress-bar" id="wwBar" role="progressbar"></div>
                            </div>
                            <small id="wwSterkte" class="text-muted"></small>
                        </div>

                        <!-- Wachtwoord bevestigen -->
                        <div class="col-md-6">
                            <label for="wachtwoord_bevestig" class="form-label fw-semibold">
                                Wachtwoord bevestigen <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="wachtwoord_bevestig"
                                    name="wachtwoord_bevestig" required minlength="8"
                                    autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="toggleWw('wachtwoord_bevestig','oog2')">
                                    <i class="bi bi-eye" id="oog2"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="bevestigFout">
                                Wachtwoorden komen niet overeen.
                            </div>
                        </div>

                        <!-- Telefoonnummer -->
                        <div class="col-md-6">
                            <label for="telefoonnummer" class="form-label fw-semibold">Telefoonnummer</label>
                            <input type="tel" class="form-control" id="telefoonnummer"
                                name="telefoonnummer" maxlength="20"
                                pattern="^(\+?[0-9][\d\s\-\.\(\)]{6,18})$"
                                placeholder="bijv. 0612345678 of 020-1234567"
                                autocomplete="tel"
                                value="<?= htmlspecialchars($oud['telefoonnummer'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="invalid-feedback">
                                Voer een geldig telefoonnummer in (bijv. 0612345678 of +31612345678).
                            </div>
                        </div>

                        <!-- Adres -->
                        <div class="col-md-6">
                            <label for="adres" class="form-label fw-semibold">Adres</label>
                            <input type="text" class="form-control" id="adres" name="adres"
                                maxlength="255" placeholder="Straat 1, 1234 AB Stad"
                                autocomplete="street-address"
                                value="<?= htmlspecialchars($oud['adres'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <!-- Allergenen -->
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
                            <div class="form-text">Typ om te zoeken. Meerdere selecteerbaar.</div>
                        </div>

                        <!-- Wensen -->
                        <div class="col-12">
                            <label for="wensen" class="form-label fw-semibold">Wensen / opmerkingen</label>
                            <textarea class="form-control" id="wensen" name="wensen"
                                rows="3" maxlength="1000"
                                placeholder="Bijv. voorkeur voor een bepaalde kapper of producten..."
                            ><?= htmlspecialchars($oud['wensen'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                            <div class="form-text" id="wensenTeller">0 / 1000 tekens</div>
                        </div>

                    </div><!-- /row -->

                    <hr class="my-4">

                    <div class="d-flex gap-2 align-items-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus me-1"></i>Account aanmaken
                        </button>
                        <span class="text-muted small">Al een account?</span>
                        <a href="/login" class="btn btn-link btn-sm p-0">Inloggen</a>
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

// Wachtwoord-sterkte
document.getElementById('wachtwoord').addEventListener('input', function () {
    const ww = this.value;
    let score = 0;
    if (ww.length >= 8)          score++;
    if (/[A-Z]/.test(ww))         score++;
    if (/[a-z]/.test(ww))         score++;
    if (/[0-9]/.test(ww))         score++;
    if (/[^A-Za-z0-9]/.test(ww))  score++;
    const labels = ['','Zeer zwak','Zwak','Redelijk','Sterk','Zeer sterk'];
    const colors = ['','danger','warning','info','primary','success'];
    const prg = document.getElementById('wwProgress');
    const bar = document.getElementById('wwBar');
    const txt = document.getElementById('wwSterkte');
    prg.hidden = ww.length === 0;
    bar.style.width = (score * 20) + '%';
    bar.className   = 'progress-bar bg-' + (colors[score] || 'secondary');
    txt.textContent = ww.length > 0 ? (labels[score] || '') : '';
});

// Wachtwoord-overeenkomst check bij submit
document.getElementById('regForm').addEventListener('submit', function (e) {
    const ww  = document.getElementById('wachtwoord').value;
    const bev = document.getElementById('wachtwoord_bevestig');
    if (ww !== bev.value) {
        bev.classList.add('is-invalid');
        document.getElementById('bevestigFout').textContent = 'Wachtwoorden komen niet overeen.';
        e.preventDefault();
    } else {
        bev.classList.remove('is-invalid');
    }
});

// Live wachtwoord-overeenkomst
document.getElementById('wachtwoord_bevestig').addEventListener('input', function () {
    const ww  = document.getElementById('wachtwoord').value;
    if (this.value && this.value !== ww) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});

// Tekenteller wensen
const wensen = document.getElementById('wensen');
const teller = document.getElementById('wensenTeller');
function updateTeller() {
    teller.textContent = wensen.value.length + ' / 1000 tekens';
    teller.className = wensen.value.length > 900 ? 'form-text text-warning' : 'form-text text-muted';
}
wensen.addEventListener('input', updateTeller);
updateTeller();
</script>
