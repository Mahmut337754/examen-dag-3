<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2 class="mb-4"><i class="bi bi-key me-2 text-primary"></i>Wachtwoord wijzigen</h2>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?= url('/wachtwoord-wijzigen') ?>" novalidate id="wachtwoordForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Huidig wachtwoord -->
                    <div class="mb-3">
                        <label for="huidig_wachtwoord" class="form-label">Huidig wachtwoord</label>
                        <input
                            type="password"
                            class="form-control"
                            id="huidig_wachtwoord"
                            name="huidig_wachtwoord"
                            required
                            autocomplete="current-password">
                        <div class="invalid-feedback">Huidig wachtwoord is verplicht.</div>
                    </div>

                    <!-- Nieuw wachtwoord -->
                    <div class="mb-3">
                        <label for="nieuw_wachtwoord" class="form-label">Nieuw wachtwoord</label>
                        <input
                            type="password"
                            class="form-control"
                            id="nieuw_wachtwoord"
                            name="nieuw_wachtwoord"
                            required
                            minlength="8"
                            autocomplete="new-password">
                        <div class="form-text">Minimaal 8 tekens.</div>
                        <div class="invalid-feedback">Nieuw wachtwoord moet minimaal 8 tekens bevatten.</div>
                    </div>

                    <!-- Bevestig nieuw wachtwoord -->
                    <div class="mb-4">
                        <label for="bevestig_wachtwoord" class="form-label">Bevestig nieuw wachtwoord</label>
                        <input
                            type="password"
                            class="form-control"
                            id="bevestig_wachtwoord"
                            name="bevestig_wachtwoord"
                            required
                            minlength="8"
                            autocomplete="new-password">
                        <div class="invalid-feedback" id="bevestigFout">Wachtwoorden komen niet overeen.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Opslaan
                        </button>
                        <a href="/dashboard" class="btn btn-outline-secondary">Annuleren</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side wachtwoord-overeenkomst controle
document.getElementById('wachtwoordForm').addEventListener('submit', function (e) {
    const nieuw     = document.getElementById('nieuw_wachtwoord');
    const bevestig  = document.getElementById('bevestig_wachtwoord');
    const foutEl    = document.getElementById('bevestigFout');

    if (nieuw.value !== bevestig.value) {
        bevestig.classList.add('is-invalid');
        foutEl.textContent = 'Wachtwoorden komen niet overeen.';
        e.preventDefault();
    } else {
        bevestig.classList.remove('is-invalid');
    }
});
</script>
