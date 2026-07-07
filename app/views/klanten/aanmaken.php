<style>
    body { background:#f0f0f0; }
    .ka-bc { font-size:.84rem; margin-bottom:.5rem; color:#555; }
    .ka-bc a { color:#555; text-decoration:none; }
    .ka-bc a:hover { text-decoration:underline; }
    .ka-h1 { font-size:1.45rem; font-weight:700; margin-bottom:1.1rem; }
    .ka-h1 span { color:#c0392b; }
    .ka-card {
        background:#fff; border:1px solid #ddd;
        border-radius:.35rem; padding:1.35rem 1.5rem;
        max-width:680px; width:100%;
    }
    .ka-text { font-size:.88rem; color:#555; }
    .ka-btn-terug {
        display:inline-flex; align-items:center; gap:.35rem;
        background:#fff; color:#333; border:1px solid #ced4da;
        border-radius:.25rem; padding:.42rem 1rem;
        font-size:.84rem; font-weight:600;
        text-decoration:none; margin-bottom:.75rem;
    }
    .ka-btn-terug:hover { background:#f0f0f0; color:#333; }
    @media (max-width:480px) { .ka-card { padding:1rem; } }
</style>

<div class="ka-bc">
    <a href="<?= url('/dashboard') ?>">Home</a> /
    <a href="<?= url('/klanten') ?>">Klanten</a> / Aanmaken
</div>

<h1 class="ka-h1"><span>Klant aanmaken</span></h1>

<div class="ka-card">
    <p class="ka-text">Functionaliteit volgt later.</p>
</div>

<div style="margin-top:.75rem;">
    <a href="<?= url('/klanten') ?>" class="ka-btn-terug">
        &#8592; Terug naar overzicht
    </a>
</div>
