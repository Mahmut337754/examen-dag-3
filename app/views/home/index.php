<style>
    .home-section {
        min-height: 80vh;
        background: #fafaf8;
        display: flex;
        align-items: center;
        padding: 2rem 1rem;
    }
    .home-inner {
        text-align: center;
        max-width: 520px;
        margin: 0 auto;
        width: 100%;
    }
    .home-inner h1 {
        font-size: clamp(1.5rem, 5vw, 2.2rem);
        font-weight: 700;
        margin-top: .75rem;
        margin-bottom: .5rem;
    }
    .home-inner .lead {
        font-size: clamp(.9rem, 3vw, 1.1rem);
        margin-bottom: 1.75rem;
    }
    .home-btns {
        display: flex;
        gap: .75rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .home-btns .btn {
        min-width: 180px;
    }
    @media (max-width: 400px) {
        .home-btns .btn { width: 100%; }
    }
</style>

<section class="home-section">
    <div class="home-inner">
        <i class="bi bi-scissors display-1" style="color:#c9a84c;"></i>
        <h1>Welkom bij Kniploket Tiko</h1>
        <p class="lead text-muted">Uw salon voor knippen, kleuren en stylen.</p>

        <div class="home-btns">
            <a href="<?= url('/registreren') ?>" class="btn btn-lg fw-bold"
               style="background:#c9a84c;color:#1a1a2e;">
                <i class="bi bi-person-plus me-2"></i>Account aanmaken
            </a>
            <a href="<?= url('/login') ?>" class="btn btn-lg btn-outline-dark">
                <i class="bi bi-box-arrow-in-right me-2"></i>Inloggen
            </a>
        </div>
    </div>
</section>
