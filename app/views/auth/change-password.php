<style>
    body { background:#f0f0f0; }
    .cp-wrap {
        max-width: 480px;
        width: 100%;
    }
    .cp-h2 { font-size:1.45rem; font-weight:700; margin-bottom:1.2rem; color:#1a1a1a; }
    .cp-card {
        background:#fff; border:1px solid #ddd;
        border-radius:.35rem; padding:1.5rem;
    }
    .cp-lbl { font-size:.82rem; font-weight:600; color:#333; display:block; margin-bottom:.25rem; }
    .cp-input {
        width:100%; border:1px solid #ced4da; border-radius:.25rem;
        padding:.42rem .65rem; font-size:.86rem; color:#333; background:#fff;
    }
    .cp-input:focus { outline:none; border-color:#c0392b; }
    .cp-hint { font-size:.75rem; color:#6c757d; margin-top:.2rem; }
    .cp-err  { font-size:.75rem; color:#dc3545; margin-top:.2rem; display:none; }
    .cp-fld  { margin-bottom:1rem; }
    .cp-actions { display:flex; gap:.5rem; margin-top:1.3rem; flex-wrap:wrap; }
    .cp-btn-save {
        background:#c0392b; color:#fff; border:none;
        border-radius:.25rem; padding:.44rem 1.2rem;
        font-size:.86rem; font-weight:600; cursor:pointer;
    }
    .cp-btn-save:hover { background:#a93226; }
    .cp-btn-cancel {
        background:#fff; color:#333; border:1px solid #ced4da;
        border-radius:.25rem; padding:.44rem 1rem;
        font-size:.86rem; font-weight:600;
        text-decoration:none; display:inline-block;
    }
    .cp-btn-cancel:hover { background:#f0f0f0; color:#333; }
    .cp-pw-wrap { position:relative; display:flex; gap:0; }
    .cp-pw-wrap .cp-input { border-radius:.25rem 0 0 .25rem; flex:1; }
    .cp-pw-toggle {
        border:1px solid #ced4da; border-left:none;
        background:#fff; color:#555; border-radius:0 .25rem .25rem 0;
        padding:0 .65rem; cursor:pointer; font-size:.9rem;
        display:flex; align-items:center;
    }
    .cp-pw-toggle:hover { background:#f8f8f8; }
    @media (max-width:480px) {
        .cp-actions { flex-direction:column; }
        .cp-btn-save, .cp-btn-cancel { text-align:center; }
    }
</style>

<div class="cp-wrap">
    <h2 class="cp-h2">Wachtwoord wijzigen</h2>

    <div class="cp-card">
        <form method="POST" action="<?= url('/wachtwoord-wijzigen') ?>" novalidate id="cpForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <!-- Huidig wachtwoord -->
            <div class="cp-fld">
                <label for="huidig_wachtwoord" class="cp-lbl">Huidig wachtwoord</label>
                <div class="cp-pw-wrap">
                    <input type="password" class="cp-input" id="huidig_wachtwoord"
                        name="huidig_wachtwoord" required autocomplete="current-password">
                    <button type="button" class="cp-pw-toggle" onclick="togglePw('huidig_wachtwoord','ico1')">
                        <i class="bi bi-eye" id="ico1"></i>
                    </button>
                </div>
                <div class="cp-err" id="huidigErr">Huidig wachtwoord is verplicht.</div>
            </div>

            <!-- Nieuw wachtwoord -->
            <div class="cp-fld">
                <label for="nieuw_wachtwoord" class="cp-lbl">Nieuw wachtwoord</label>
                <div class="cp-pw-wrap">
                    <input type="password" class="cp-input" id="nieuw_wachtwoord"
                        name="nieuw_wachtwoord" required minlength="8" autocomplete="new-password">
                    <button type="button" class="cp-pw-toggle" onclick="togglePw('nieuw_wachtwoord','ico2')">
                        <i class="bi bi-eye" id="ico2"></i>
                    </button>
                </div>
                <div class="cp-hint">Minimaal 8 tekens.</div>
                <div class="cp-err" id="nieuwErr">Nieuw wachtwoord moet minimaal 8 tekens bevatten.</div>
            </div>

            <!-- Bevestig nieuw wachtwoord -->
            <div class="cp-fld">
                <label for="bevestig_wachtwoord" class="cp-lbl">Bevestig nieuw wachtwoord</label>
                <div class="cp-pw-wrap">
                    <input type="password" class="cp-input" id="bevestig_wachtwoord"
                        name="bevestig_wachtwoord" required minlength="8" autocomplete="new-password">
                    <button type="button" class="cp-pw-toggle" onclick="togglePw('bevestig_wachtwoord','ico3')">
                        <i class="bi bi-eye" id="ico3"></i>
                    </button>
                </div>
                <div class="cp-err" id="bevestigErr">Wachtwoorden komen niet overeen.</div>
            </div>

            <div class="cp-actions">
                <button type="submit" class="cp-btn-save">Opslaan</button>
                <a href="<?= url('/dashboard') ?>" class="cp-btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>
</div>

<script>
function togglePw(veldId, icoId) {
    const v = document.getElementById(veldId);
    const i = document.getElementById(icoId);
    v.type = v.type === 'password' ? 'text' : 'password';
    i.classList.toggle('bi-eye');
    i.classList.toggle('bi-eye-slash');
}

document.getElementById('cpForm').addEventListener('submit', function (e) {
    let ok = true;
    const huidig    = document.getElementById('huidig_wachtwoord');
    const nieuw     = document.getElementById('nieuw_wachtwoord');
    const bevestig  = document.getElementById('bevestig_wachtwoord');

    document.querySelectorAll('.cp-err').forEach(el => el.style.display = 'none');

    if (!huidig.value.trim()) {
        document.getElementById('huidigErr').style.display = 'block'; ok = false;
    }
    if (nieuw.value.length < 8) {
        document.getElementById('nieuwErr').style.display = 'block'; ok = false;
    }
    if (nieuw.value !== bevestig.value) {
        document.getElementById('bevestigErr').style.display = 'block'; ok = false;
    }
    if (!ok) e.preventDefault();
});
</script>
