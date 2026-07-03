<section class="py-5 text-center" style="min-height:80vh;background:#fafaf8;">
    <div class="container">
        <i class="bi bi-scissors display-1" style="color:var(--salon-gold);"></i>
        <h1 class="fw-bold mt-3">Welkom bij Kniploket Tiko</h1>
        <p class="lead text-muted mb-4">
            Uw salon voor knippen, kleuren en stylen.
        </p>

        <div class="d-flex gap-3 justify-content-center">
            <a href="<?= url('/registreren') ?>" class="btn btn-lg fw-bold" 
               style="background:var(--salon-gold);color:#1a1a2e;">
                <i class="bi bi-person-plus me-2"></i>Account aanmaken
            </a>
            <a href="<?= url('/login') ?>" class="btn btn-lg btn-outline-dark">
                <i class="bi bi-box-arrow-in-right me-2"></i>Inloggen
            </a>
        </div>
    </div>
</section>
