<style>
    .login-section {
        min-height: 80vh;
        background: #fafaf8;
        display: flex;
        align-items: center;
        padding: 2rem 1rem;
    }
    .login-inner {
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
    }
    .login-header { text-align:center; margin-bottom:1.5rem; }
    .login-header h2 { font-size: clamp(1.3rem, 5vw, 1.75rem); font-weight:700; margin-top:.5rem; }
    .login-header p  { font-size:.88rem; color:#666; }

    .login-card {
        background:#fff; border:1px solid #ddd;
        border-radius:.5rem; padding:1.75rem 1.75rem 1.5rem;
    }
    .lg-lbl { font-size:.82rem; font-weight:600; color:#333; display:block; margin-bottom:.25rem; }
    .lg-fld { margin-bottom:1rem; }
    .lg-input {
        width:100%; border:1px solid #ced4da; border-radius:.25rem;
        padding:.44rem .7rem; font-size:.87rem; color:#333;
        background:#fff; box-sizing:border-box;
    }
    .lg-input:focus { outline:none; border-color:#c0392b; }
    .lg-pw-wrap { display:flex; }
    .lg-pw-wrap .lg-input { border-radius:.25rem 0 0 .25rem; }
    .lg-pw-toggle {
        border:1px solid #ced4da; border-left:none; background:#fff;
        border-radius:0 .25rem .25rem 0; padding:0 .7rem;
        cursor:pointer; color:#555; font-size:.9rem;
        display:flex; align-items:center;
    }
    .lg-pw-toggle:hover { background:#f8f8f8; }
    .lg-btn {
        display:block; width:100%;
        background:#c9a84c; color:#1a1a2e; border:none;
        border-radius:.25rem; padding:.56rem 1rem;
        font-size:.95rem; font-weight:700; cursor:pointer;
        margin-top:.5rem;
    }
    .lg-btn:hover { background:#b8973e; }
    .lg-divider { border:none; border-top:1px solid #eee; margin:1.1rem 0; }
    .lg-links { text-align:center; font-size:.83rem; color:#666; }
    .lg-links a { color:#c0392b; text-decoration:none; font-weight:600; }
    .lg-links a:hover { text-decoration:underline; }
    @media (max-width:480px) {
        .login-card { padding:1.25rem 1rem; }
    }
</style>

<div class="login-section">
    <div class="login-inner">
        <div class="login-header">
            <i class="bi bi-scissors" style="font-size:2.5rem; color:#c9a84c;"></i>
            <h2>Inloggen</h2>
            <p>Welkom terug bij Kniploket Tiko</p>
        </div>

        <div class="login-card">
            <form method="POST" action="<?= url('/login') ?>" novalidate id="loginForm">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                <div class="lg-fld">
                    <label for="email" class="lg-lbl">E-mailadres</label>
                    <input type="email" class="lg-input" id="email" name="email"
                        required autocomplete="email" maxlength="255"
                        placeholder="naam@voorbeeld.nl">
                </div>

                <div class="lg-fld">
                    <label for="wachtwoord" class="lg-lbl">Wachtwoord</label>
                    <div class="lg-pw-wrap">
                        <input type="password" class="lg-input" id="wachtwoord"
                            name="wachtwoord" required autocomplete="current-password"
                            placeholder="Wachtwoord">
                        <button type="button" class="lg-pw-toggle" id="toggleWw" tabindex="-1">
                            <i class="bi bi-eye" id="oogIcoon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="lg-btn">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Inloggen
                </button>
            </form>

            <hr class="lg-divider">

            <div class="lg-links">
                Nog geen account?
                <a href="<?= url('/registreren') ?>">Account aanmaken</a>
                &nbsp;·&nbsp;
                <a href="<?= url('/') ?>" style="color:#888; font-weight:400;">
                    <i class="bi bi-arrow-left"></i> Terug
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('toggleWw').addEventListener('click', function () {
    const veld  = document.getElementById('wachtwoord');
    const icoon = document.getElementById('oogIcoon');
    veld.type = veld.type === 'password' ? 'text' : 'password';
    icoon.classList.toggle('bi-eye');
    icoon.classList.toggle('bi-eye-slash');
});
</script>
