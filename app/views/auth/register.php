<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">

<section class="py-5" style="min-height:80vh;background:#fafaf8;">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-7 col-md-9">

    <div class="text-center mb-4">
        <i class="bi bi-scissors fs-1" style="color:var(--salon-gold, #c9a84c);"></i>
        <h2 class="fw-bold mt-2">Account aanmaken</h2>
        <p class="text-muted">Maak een gratis klantaccount aan bij Kniploket Tiko</p>
    </div>

    <div class="card shadow-sm border-0" style="border-radius:1rem;">
        <div class="card-body p-4 p-md-5">
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
                            placeholder="Voor- en achternaam"
                            value="<?= htmlspecialchars($oud['naam'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <div class="invalid-feedback">Naam is verplicht (min. 2 tekens).</div>
                    </div>

                    <!-- E-mail -->
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">
                            E-mailadres <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                            required maxlength="255"
                            placeholder="naam@voorbeeld.nl"
                            value="<?= htmlspecialchars($oud['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <div class="invalid-feedback">Voer een geldig e-mailadres in.</div>
                    </div>

                    <!-- Wachtwoord -->
                    <div class="col-md-6">
                        <label for="wachtwoord" class="form-label fw-semibold">
                            Wachtwoord <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="wachtwoord"
                                name="wachtwoord" required minlength="8" maxlength="72"
                                autocomplete="new-password" placeholder="Min. 8 tekens">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="toggleWw('wachtwoord','oog1')">
                                <i class="bi bi-eye" id="oog1"></i>
                            </button>
                        </div>
                        <div class="form-text">Min. 8 tekens, 1 hoofdletter, 1 kleine letter, 1 cijfer.</div>
                        <!-- Sterkte-indicator -->
                        <div class="progress mt-1" style="height:4px;" id="wwProgress" hidden>
                            <div class="progress-bar" id="wwBar" role="progressbar"></div>
                        </div>
                        <small id="wwSterkte" class="text-muted"></small>
                    </div>

                    <!-- Wachtwoord bevestigen -->
                    <div class="col-md-6">
                        <label for="wachtwoord2" class="form-label fw-semibold">
                            Wachtwoord bevestigen <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="wachtwoord2"
                                name="wachtwoord2" required minlength="8"
                                autocomplete="new-password" placeholder="Herhaal wachtwoord">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="toggleWw('wachtwoord2','oog2')">
                                <i class="bi bi-eye" id="oog2"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="wwMatchFout">
                            Wachtwoorden komen niet overeen.
                        </div>
                    </div>

                    <!-- Telefoonnummer -->
                    <div class="col-md-6">
                        <label for="telefoonnummer" class="form-label fw-semibold">Telefoonnummer</label>
                        <input type="tel" class="form-control" id="telefoonnummer"
                            name="telefoonnummer" maxlength="20"
                            pattern="^(\+?[0-9][\d\s\-\.\(\)]{6,18})$"
                            placeholder="bijv. 0612345678"
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
                            value="<?= htmlspecialchars($oud['adres'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <!-- Allergenen -->
                    <div class="col-12">
                        <label for="allergenen" class="form-label fw-semibold">
                            <i class="bi bi-exclamation-triangle text-danger me-1"></i>Allergenen
                        </label>
                        <select id="allergenen" name="allergenen[]" multiple class="form-select">
                            <?php foreach ($alleAllergenen as $al): ?>
                                <option value="<?= (int)$al['id'] ?>"
                                    <?= in_array((int)$al['id'], array_map('intval', $geselecteerd)) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($al['naam'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Typ om te zoeken. Selecteer alle stoffen waar je allergisch voor bent.</div>
                    </div>

                    <!-- Wensen -->
                    <div class="col-12">
                        <label for="wensen" class="form-label fw-semibold">Wensen & voorkeuren</label>
                        <textarea class="form-control" id="wensen" name="wensen"
                            rows="3" maxlength="1000"
                            placeholder="Bijv. voorkeur voor ammoniakvrije producten, altijd blowdry..."
                        ><?= htmlspecialchars($oud['wensen'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        <div class="form-text" id="wensenTeller">0 / 1000 tekens</div>
                    </div>

                </div><!-- /row -->

                <hr class="my-4">

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-lg fw-bold"
                        style="background:var(--salon-gold,#c9a84c);color:#1a1a2e;">
                        <i class="bi bi-check-circle me-2"></i>Account aanmaken
                    </button>
                </div>

                <p class="text-center text-muted small mt-3 mb-0">
                    Al een account?
                    <a href="/login" class="text-decoration-none fw-semibold">Inloggen</a>
                </p>
            </form>
        </div>
    </div>

</div>
</div>
</div>
</section>

<!-- Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
<script>
new Choices('#allergenen', {
    removeItemButton: true,
    searchEnabled: true,
    searchPlaceholderValue: 'Zoek allergeen...',
    itemSelectText: '',
    noResultsText: 'Geen resultaten gevonden',
    placeholder: true,
    placeholderValue: 'Selecteer allergenen...',
});

function toggleWw(id, oogId) {
    const v = document.getElementById(id);
    const i = document.getElementById(oogId);
    v.type = v.type === 'password' ? 'text' : 'password';
    i.classList.toggle('bi-eye');
    i.classList.toggle('bi-eye-slash');
}

// Sterkte-indicator
document.getElementById('wachtwoord').addEventListener('input', function () {
    const ww = this.value;
    const bar = document.getElementById('wwBar');
    const txt = document.getElementById('wwSterkte');
    const prg = document.getElementById('wwProgress');
    let score = 0;
    if (ww.length >= 8)           score++;
    if (/[A-Z]/.test(ww))          score++;
    if (/[a-z]/.test(ww))          score++;
    if (/[0-9]/.test(ww))          score++;
    if (/[^A-Za-z0-9]/.test(ww))   score++;
    const labels = ['','Zeer zwak','Zwak','Redelijk','Sterk','Zeer sterk'];
    const colors = ['','danger','warning','info','primary','success'];
    prg.hidden = ww.length === 0;
    bar.style.width = (score * 20) + '%';
    bar.className   = 'progress-bar bg-' + (colors[score] || 'secondary');
    txt.textContent = ww.length > 0 ? (labels[score] || '') : '';
});

// Wachtwoord-overeenkomst check bij submit
document.getElementById('regForm').addEventListener('submit', function (e) {
    const ww1 = document.getElementById('wachtwoord');
    const ww2 = document.getElementById('wachtwoord2');
    if (ww1.value !== ww2.value) {
        ww2.classList.add('is-invalid');
        document.getElementById('wwMatchFout').textContent = 'Wachtwoorden komen niet overeen.';
        e.preventDefault();
        e.stopPropagation();
    } else {
        ww2.classList.remove('is-invalid');
    }
    this.classList.add('was-validated');
});

// Tekenteller wensen
const wensen = document.getElementById('wensen');
const teller = document.getElementById('wensenTeller');
wensen.addEventListener('input', () => {
    teller.textContent = wensen.value.length + ' / 1000 tekens';
});
</script>
