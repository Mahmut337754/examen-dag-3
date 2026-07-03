<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">

<style>
    .reg-wrap {
        max-width: 660px;
        width: 100%;
        margin: 0 auto;
        padding: 1.5rem 0;
    }
    .reg-header { text-align:center; margin-bottom:1.5rem; }
    .reg-header h2 { font-size: clamp(1.25rem, 5vw, 1.65rem); font-weight:700; margin-top:.4rem; }
    .reg-header p  { font-size:.87rem; color:#666; }

    .reg-card {
        background:#fff; border:1px solid #ddd;
        border-radius:.4rem; padding:1.5rem;
    }
    .reg-grid { display:grid; grid-template-columns:1fr 1fr; gap:.85rem; }
    .reg-full  { grid-column:1 / -1; }
    .reg-grp   { display:flex; flex-direction:column; gap:.22rem; }
    .reg-lbl   { font-size:.81rem; font-weight:600; color:#333; }
    .reg-lbl .req { color:#c0392b; }

    .reg-input, .reg-select, .reg-textarea {
        width:100%; border:1px solid #ced4da; border-radius:.25rem;
        padding:.42rem .65rem; font-size:.85rem; color:#333;
        background:#fff; box-sizing:border-box;
    }
    .reg-input:focus, .reg-select:focus, .reg-textarea:focus {
        outline:none; border-color:#c0392b;
    }
    .reg-textarea { resize:vertical; min-height:75px; }
    .reg-hint  { font-size:.74rem; color:#6c757d; }
    .reg-err   { font-size:.74rem; color:#dc3545; display:none; }
    .reg-pw-wrap { display:flex; }
    .reg-pw-wrap .reg-input { border-radius:.25rem 0 0 .25rem; }
    .reg-pw-btn {
        border:1px solid #ced4da; border-left:none; background:#fff;
        border-radius:0 .25rem .25rem 0; padding:0 .65rem;
        cursor:pointer; color:#555; font-size:.88rem;
        display:flex; align-items:center;
    }
    .reg-pw-btn:hover { background:#f8f8f8; }

    .reg-divider { border:none; border-top:1px solid #eee; margin:1.25rem 0; }
    .reg-actions { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }
    .reg-btn-submit {
        background:#c0392b; color:#fff; border:none;
        border-radius:.25rem; padding:.46rem 1.3rem;
        font-size:.87rem; font-weight:700; cursor:pointer;
    }
    .reg-btn-submit:hover { background:#a93226; }
    .reg-login-link { font-size:.83rem; color:#555; }
    .reg-login-link a { color:#c0392b; text-decoration:none; font-weight:600; }
    .reg-login-link a:hover { text-decoration:underline; }

    /* Sterkte-balk */
    .reg-strength { height:4px; border-radius:2px; margin-top:.3rem; background:#eee; overflow:hidden; }
    .reg-strength-bar { height:100%; width:0; transition:width .2s, background .2s; border-radius:2px; }

    @media (max-width: 540px) {
        .reg-grid  { grid-template-columns:1fr; }
        .reg-full  { grid-column:1; }
        .reg-card  { padding:1rem; }
        .reg-actions { flex-direction:column; align-items:stretch; }
        .reg-btn-submit { text-align:center; }
    }
</style>

<div class="reg-wrap">
    <div class="reg-header">
        <i class="bi bi-scissors" style="font-size:2.2rem; color:#c9a84c;"></i>
        <h2>Account aanmaken</h2>
        <p>Maak een gratis klantaccount aan bij Kniploket Tiko</p>
    </div>

    <div class="reg-card">
        <form method="POST" action="<?= url('/registreren') ?>" novalidate id="regForm">
            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="reg-grid">

                <!-- Naam -->
                <div class="reg-grp">
                    <label for="naam" class="reg-lbl">Naam <span class="req">*</span></label>
                    <input type="text" id="naam" name="naam" class="reg-input"
                        required minlength="2" maxlength="100" autocomplete="name"
                        placeholder="Voor- en achternaam"
                        value="<?= htmlspecialchars($oud['naam'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <div class="reg-err" id="naamErr">Naam is verplicht (min. 2 tekens).</div>
                </div>

                <!-- E-mail -->
                <div class="reg-grp">
                    <label for="email" class="reg-lbl">E-mailadres <span class="req">*</span></label>
                    <input type="email" id="email" name="email" class="reg-input"
                        required maxlength="255" autocomplete="email"
                        placeholder="naam@voorbeeld.nl"
                        value="<?= htmlspecialchars($oud['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <div class="reg-err" id="emailErr">Voer een geldig e-mailadres in.</div>
                </div>

                <!-- Wachtwoord -->
                <div class="reg-grp">
                    <label for="wachtwoord" class="reg-lbl">Wachtwoord <span class="req">*</span></label>
                    <div class="reg-pw-wrap">
                        <input type="password" id="wachtwoord" name="wachtwoord" class="reg-input"
                            required minlength="8" maxlength="72" autocomplete="new-password">
                        <button type="button" class="reg-pw-btn" onclick="toggleWw('wachtwoord','oog1')">
                            <i class="bi bi-eye" id="oog1"></i>
                        </button>
                    </div>
                    <div class="reg-hint">Min. 8 tekens, 1 hoofdletter, 1 kleine letter, 1 cijfer.</div>
                    <div class="reg-strength" id="wwProgress" hidden>
                        <div class="reg-strength-bar" id="wwBar"></div>
                    </div>
                    <small id="wwSterkte" class="reg-hint"></small>
                    <div class="reg-err" id="wwErr">Wachtwoord voldoet niet aan de eisen.</div>
                </div>

                <!-- Wachtwoord bevestigen -->
                <div class="reg-grp">
                    <label for="wachtwoord_bevestig" class="reg-lbl">Bevestig wachtwoord <span class="req">*</span></label>
                    <div class="reg-pw-wrap">
                        <input type="password" id="wachtwoord_bevestig" name="wachtwoord_bevestig"
                            class="reg-input" required minlength="8" autocomplete="new-password">
                        <button type="button" class="reg-pw-btn" onclick="toggleWw('wachtwoord_bevestig','oog2')">
                            <i class="bi bi-eye" id="oog2"></i>
                        </button>
                    </div>
                    <div class="reg-err" id="bevestigErr">Wachtwoorden komen niet overeen.</div>
                </div>

                <!-- Telefoonnummer -->
                <div class="reg-grp">
                    <label for="telefoonnummer" class="reg-lbl">Telefoonnummer</label>
                    <input type="tel" id="telefoonnummer" name="telefoonnummer" class="reg-input"
                        maxlength="20" autocomplete="tel"
                        placeholder="bijv. 0612345678"
                        value="<?= htmlspecialchars($oud['telefoonnummer'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <!-- Adres -->
                <div class="reg-grp">
                    <label for="adres" class="reg-lbl">Adres</label>
                    <input type="text" id="adres" name="adres" class="reg-input"
                        maxlength="255" autocomplete="street-address"
                        placeholder="Straat 1, 1234 AB Stad"
                        value="<?= htmlspecialchars($oud['adres'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <!-- Allergenen -->
                <div class="reg-grp reg-full">
                    <label for="allergenen" class="reg-lbl">
                        <i class="bi bi-exclamation-triangle" style="color:#dc3545;"></i> Allergenen
                    </label>
                    <select id="allergenen" name="allergenen[]" multiple class="reg-select">
                        <?php foreach ($alleAllergenen as $al): ?>
                            <option value="<?= (int)$al['id'] ?>"
                                <?= in_array((int)$al['id'], array_map('intval', $geselecteerd)) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($al['naam'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="reg-hint">Typ om te zoeken. Meerdere selecteerbaar.</div>
                </div>

                <!-- Wensen -->
                <div class="reg-grp reg-full">
                    <label for="wensen" class="reg-lbl">Wensen &amp; voorkeuren</label>
                    <textarea id="wensen" name="wensen" class="reg-textarea"
                        maxlength="1000"
                        placeholder="Bijv. voorkeur voor ammoniakvrije producten..."
                    ><?= htmlspecialchars($oud['wensen'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <div class="reg-hint" id="wensenTeller">0 / 1000 tekens</div>
                </div>

            </div><!-- /grid -->

            <hr class="reg-divider">

            <div class="reg-actions">
                <button type="submit" class="reg-btn-submit">
                    <i class="bi bi-check-circle me-1"></i>Account aanmaken
                </button>
                <div class="reg-login-link">
                    Al een account? <a href="<?= url('/login') ?>">Inloggen</a>
                </div>
            </div>
        </form>
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
    const colors = ['#dc3545','#dc3545','#fd7e14','#0dcaf0','#0d6efd','#198754'];
    const prg = document.getElementById('wwProgress');
    const bar = document.getElementById('wwBar');
    const txt = document.getElementById('wwSterkte');
    prg.hidden = ww.length === 0;
    bar.style.width  = (score * 20) + '%';
    bar.style.background = colors[score] || '#eee';
    txt.textContent  = ww.length > 0 ? (labels[score] || '') : '';
});

// Live bevestig check
document.getElementById('wachtwoord_bevestig').addEventListener('input', function () {
    const ww = document.getElementById('wachtwoord').value;
    const err = document.getElementById('bevestigErr');
    err.style.display = (this.value && this.value !== ww) ? 'block' : 'none';
});

// Submit validatie
document.getElementById('regForm').addEventListener('submit', function (e) {
    let ok = true;
    const ww  = document.getElementById('wachtwoord');
    const bev = document.getElementById('wachtwoord_bevestig');

    if (ww.value !== bev.value) {
        document.getElementById('bevestigErr').style.display = 'block';
        ok = false;
    }
    if (!ok) e.preventDefault();
});

// Tekenteller wensen
const wensen = document.getElementById('wensen');
const teller = document.getElementById('wensenTeller');
function updateTeller() {
    const n = wensen.value.length;
    teller.textContent  = n + ' / 1000 tekens';
    teller.style.color  = n > 900 ? '#fd7e14' : '#6c757d';
}
wensen.addEventListener('input', updateTeller);
updateTeller();
</script>
