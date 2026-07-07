<section class="py-5" style="min-height:80vh;background:#fafaf8;">
<div class="container">
<div class="row justify-content-center">
<div class="col-md-5 col-lg-4">

    <div class="text-center mb-4">
        <i class="bi bi-scissors fs-1" style="color:var(--salon-gold, #c9a84c);"></i>
        <h2 class="fw-bold mt-2">Inloggen</h2>
        <p class="text-muted">Welkom terug bij Kniploket Tiko</p>
    </div>

    <div class="card shadow-sm border-0" style="border-radius:1rem;">
        <div class="card-body p-4">
            <form method="POST" action="<?= url('/login') ?>" novalidate id="loginForm">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">E-mailadres</label>
                    <input type="email" class="form-control" id="email" name="email"
                        required autocomplete="email" maxlength="255"
                        placeholder="naam@voorbeeld.nl">
                    <div class="invalid-feedback">Voer een geldig e-mailadres in.</div>
                </div>

                <div class="mb-4">
                    <label for="wachtwoord" class="form-label fw-semibold">Wachtwoord</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="wachtwoord"
                            name="wachtwoord" required autocomplete="current-password"
                            placeholder="Wachtwoord">
                        <button class="btn btn-outline-secondary" type="button"
                            id="toggleWachtwoord" tabindex="-1">
                            <i class="bi bi-eye" id="oogIcoon"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">Wachtwoord is verplicht.</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-lg fw-bold"
                        style="background:var(--salon-gold,#c9a84c);color:#1a1a2e;">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Inloggen
                    </button>
                </div>
            </form>

            <hr class="my-3">

            <p class="text-center text-muted small mb-0">
                Nog geen account?
                <a href="/registreren" class="text-decoration-none fw-semibold">Account aanmaken</a>
            </p>
            <p class="text-center mt-2 mb-0">
                <a href="/" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Terug naar homepagina
                </a>
            </p>
        </div>
    </div>

</div>
</div>
</div>
</section>

<script>
document.getElementById('toggleWachtwoord').addEventListener('click', function () {
    const veld  = document.getElementById('wachtwoord');
    const icoon = document.getElementById('oogIcoon');
    veld.type = veld.type === 'password' ? 'text' : 'password';
    icoon.classList.toggle('bi-eye');
    icoon.classList.toggle('bi-eye-slash');
});
</script>
