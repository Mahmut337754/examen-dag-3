<style>
    body { background:#f0f0f0; }
    .pd-bc { font-size:.84rem; margin-bottom:.5rem; color:#555; }
    .pd-bc a { color:#555; text-decoration:none; }
    .pd-bc a:hover { text-decoration:underline; }
    .pd-h1 { color:#c0392b; font-size:1.45rem; font-weight:700; margin-bottom:1rem; }
    .pd-card {
        background:#fff; border:1px solid #ddd;
        border-radius:.35rem; padding:1.1rem 1.25rem;
        max-width:600px; width:100%;
        font-size:.87rem; color:#555;
    }
    .pd-btn-terug {
        display:inline-flex; align-items:center; gap:.35rem;
        background:#fff; color:#333; border:1px solid #ced4da;
        border-radius:.25rem; padding:.42rem 1rem;
        font-size:.84rem; font-weight:600;
        text-decoration:none; margin-top:.75rem;
    }
    .pd-btn-terug:hover { background:#f0f0f0; color:#333; }
</style>

<div class="pd-bc">
    <a href="<?= url('/dashboard') ?>">Home</a> /
    <a href="<?= url('/producten') ?>">Producten</a> / Detail
</div>

<h1 class="pd-h1">Productdetail</h1>

<div class="pd-card">
    Productdetails volgen later.
</div>

<div>
    <a href="<?= url('/producten') ?>" class="pd-btn-terug">&#8592; Terug naar overzicht</a>
</div>
