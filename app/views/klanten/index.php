<?php $b = rtrim($base ?? '', '/'); ?>

<!-- ── Bevestigingsmodal ── -->
<div id="bevestigOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);
            z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:32px 28px;max-width:400px;
                width:calc(100% - 32px);box-shadow:0 20px 60px rgba(0,0,0,.18);
                font-family:inherit;animation:fadeIn .18s ease;">
        <!-- Icoon -->
        <div style="width:52px;height:52px;border-radius:50%;background:#fee2e2;
                    display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="#ef4444"
                      stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <!-- Tekst -->
        <p style="font-size:1.05rem;font-weight:700;color:#0f172a;margin:0 0 6px;">
            Klant verwijderen
        </p>
        <p style="font-size:.9rem;color:#64748b;margin:0 0 6px;">
            Weet je zeker dat je
            <strong id="modalNaam" style="color:#0f172a;"></strong>
            wilt verwijderen?
        </p>
        <p style="font-size:.8rem;color:#94a3b8;margin:0 0 24px;">
            Alle gegevens, allergieën en afspraken worden permanent verwijderd.
        </p>
        <!-- Knoppen -->
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="sluitModal()"
                    style="padding:9px 20px;border-radius:8px;border:1px solid #e2e8f0;
                           background:#fff;color:#475569;font-size:.875rem;font-weight:600;
                           cursor:pointer;">
                Annuleren
            </button>
            <button id="bevestigKnop"
                    style="padding:9px 20px;border-radius:8px;border:none;
                           background:#ef4444;color:#fff;font-size:.875rem;font-weight:600;
                           cursor:pointer;display:flex;align-items:center;gap:6px;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="#fff"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Ja, verwijderen
            </button>
        </div>
    </div>
</div>

<!-- Verborgen verwijderformulier (één per pagina) -->
<form method="POST" action="<?= $b ?>/klanten/verwijderen"
      id="verwijderForm" style="display:none;">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id" id="verwijderIdInput" value="">
</form>

<!-- ── Toast melding ── -->
<div id="annuleerToast"
     style="display:none;position:fixed;bottom:24px;right:24px;z-index:99999;
            background:#1e293b;color:#f1f5f9;padding:12px 20px;border-radius:10px;
            font-size:.875rem;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.2);
            display:flex;align-items:center;gap:10px;animation:slideUp .2s ease;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="9" stroke="#94a3b8" stroke-width="2"/>
        <path d="M15 9l-6 6M9 9l6 6" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
    </svg>
    Actie geannuleerd
</div>

<style>
@keyframes fadeIn  { from { opacity:0; transform:scale(.97); } to { opacity:1; transform:scale(1); } }
@keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
</style>

<!-- Pagina header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0" style="color:#0f172a;">Klanten</h2>
        <p class="text-muted small mb-0">
            <?= count($klanten) ?> klant<?= count($klanten) !== 1 ? 'en' : '' ?> gevonden
        </p>
    </div>
    <a href="<?= $b ?>/klanten/aanmaken" class="btn btn-primary rounded-3 px-4">
        <i class="bi bi-person-plus me-2"></i>Klant toevoegen
    </a>
</div>

<?php if (empty($klanten)): ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body text-center py-5">
        <i class="bi bi-people text-primary" style="font-size:3rem;opacity:.3;"></i>
        <h5 class="fw-semibold mt-3 mb-1">Nog geen klanten</h5>
        <p class="text-muted mb-4">Er zijn op dit moment geen klanten in het systeem.</p>
        <a href="<?= $b ?>/klanten/aanmaken" class="btn btn-primary rounded-3 px-4">
            <i class="bi bi-person-plus me-2"></i>Eerste klant toevoegen
        </a>
    </div>
</div>

<?php else: ?>

<!-- Zoekbalk -->
<div class="mb-3">
    <div class="input-group rounded-3 overflow-hidden border bg-white">
        <span class="input-group-text bg-white border-0 ps-3">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="search" id="zoekBalk" class="form-control border-0 shadow-none"
               placeholder="Zoeken op naam, e-mail of telefoon...">
    </div>
</div>

<!-- Tabel -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="klantenTabel">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="ps-4 py-3 border-0 text-muted fw-semibold"
                        style="font-size:.8rem;text-transform:uppercase;">Naam</th>
                    <th class="py-3 border-0 text-muted fw-semibold"
                        style="font-size:.8rem;text-transform:uppercase;">E-mail</th>
                    <th class="py-3 border-0 text-muted fw-semibold d-none d-md-table-cell"
                        style="font-size:.8rem;text-transform:uppercase;">Telefoon</th>
                    <th class="py-3 border-0 text-muted fw-semibold"
                        style="font-size:.8rem;text-transform:uppercase;">Status</th>
                    <th class="py-3 border-0 pe-4 text-end text-muted fw-semibold"
                        style="font-size:.8rem;text-transform:uppercase;">Acties</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($klanten as $klant): ?>
                <tr style="border-top:1px solid #f1f5f9;">
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:36px;height:36px;background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                                <span class="text-white fw-bold" style="font-size:.8rem;">
                                    <?= strtoupper(mb_substr($klant['naam'] ?? 'K', 0, 1)) ?>
                                </span>
                            </div>
                            <span class="fw-semibold text-dark">
                                <?= htmlspecialchars($klant['naam'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                    </td>
                    <td class="py-3 text-muted" style="font-size:.9rem;">
                        <?= htmlspecialchars($klant['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="py-3 text-muted d-none d-md-table-cell" style="font-size:.9rem;">
                        <?= htmlspecialchars($klant['telefoonnummer'] ?? '–', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="py-3">
                        <?php if ($klant['is_actief']): ?>
                            <span class="badge rounded-pill px-3 py-2"
                                  style="background:#dcfce7;color:#15803d;font-size:.78rem;font-weight:600;">
                                Actief
                            </span>
                        <?php else: ?>
                            <span class="badge rounded-pill px-3 py-2"
                                  style="background:#f1f5f9;color:#64748b;font-size:.78rem;font-weight:600;">
                                Inactief
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 pe-4 text-end">
                        <div class="d-inline-flex gap-1">
                            <a href="<?= $b ?>/klanten/detail?id=<?= (int)$klant['id'] ?>"
                               class="btn btn-sm btn-light border rounded-3" title="Details">
                                <i class="bi bi-person-lines-fill" style="color:#0ea5e9;"></i>
                            </a>
                            <a href="<?= $b ?>/klanten/wijzigen?id=<?= (int)$klant['id'] ?>"
                               class="btn btn-sm btn-light border rounded-3" title="Wijzigen">
                                <i class="bi bi-pencil" style="color:#6366f1;"></i>
                            </a>
                            <!-- Verwijderknop opent custom modal -->
                            <button type="button"
                                    class="btn btn-sm btn-light border rounded-3"
                                    title="Verwijderen"
                                    onclick="openModal(<?= (int)$klant['id'] ?>, '<?= htmlspecialchars(addslashes($klant['naam'] ?? ''), ENT_QUOTES, 'UTF-8') ?>')">
                                <i class="bi bi-trash" style="color:#ef4444;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="geenResultaten" class="text-center py-5 d-none">
    <i class="bi bi-search text-muted" style="font-size:2rem;"></i>
    <p class="text-muted mt-2 mb-0">Geen klanten gevonden.</p>
</div>

<?php endif; ?>

<script>
// ── Modal helpers ──
function openModal(id, naam) {
    document.getElementById('verwijderIdInput').value    = id;
    document.getElementById('modalNaam').textContent     = naam;
    var overlay = document.getElementById('bevestigOverlay');
    overlay.style.display = 'flex';
}

function sluitModal() {
    document.getElementById('bevestigOverlay').style.display = 'none';
    toonToast();
}

function toonToast() {
    var toast = document.getElementById('annuleerToast');
    toast.style.display = 'flex';
    // Fade out na 2.5 seconden
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(function () {
        toast.style.transition = 'opacity .3s';
        toast.style.opacity = '0';
        setTimeout(function () {
            toast.style.display = 'none';
            toast.style.opacity = '1';
            toast.style.transition = '';
        }, 300);
    }, 2500);
}

// Bevestig → submit formulier
document.getElementById('bevestigKnop').addEventListener('click', function () {
    document.getElementById('verwijderForm').submit();
});

// Klik buiten modal → sluiten
document.getElementById('bevestigOverlay').addEventListener('click', function (e) {
    if (e.target === this) sluitModal();
});

// ── Zoekfunctie ──
var zoek = document.getElementById('zoekBalk');
if (zoek) {
    zoek.addEventListener('input', function () {
        var term  = this.value.toLowerCase();
        var rijen = document.querySelectorAll('#klantenTabel tbody tr');
        var n     = 0;
        rijen.forEach(function (r) {
            var zichtbaar = r.textContent.toLowerCase().indexOf(term) !== -1;
            r.style.display = zichtbaar ? '' : 'none';
            if (zichtbaar) n++;
        });
        var geen = document.getElementById('geenResultaten');
        if (geen) geen.classList.toggle('d-none', n > 0 || term === '');
    });
}
</script>
