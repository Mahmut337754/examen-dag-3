<h2 class="mb-4"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h2>

<div class="row g-4 mb-5">

    <!-- Klanten -->
    <div class="col-sm-6 col-xl-3">
        <div class="card card-stat shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1 text-primary"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="fs-2 fw-bold">
                        <?= (int)($statistieken['aantal_klanten'] ?? 0) ?>
                    </div>
                    <div class="text-muted small">Klanten</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/klanten" class="btn btn-sm btn-outline-primary w-100">
                    Beheren <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Geplande afspraken -->
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100" style="border-left: 4px solid #198754;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1 text-success"><i class="bi bi-calendar-check-fill"></i></div>
                <div>
                    <div class="fs-2 fw-bold">
                        <?= (int)($statistieken['geplande_afspraken'] ?? 0) ?>
                    </div>
                    <div class="text-muted small">Geplande afspraken</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medewerkers -->
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100" style="border-left: 4px solid #fd7e14;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1 text-warning"><i class="bi bi-person-badge-fill"></i></div>
                <div>
                    <div class="fs-2 fw-bold">
                        <?= (int)($statistieken['aantal_medewerkers'] ?? 0) ?>
                    </div>
                    <div class="text-muted small">Medewerkers</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Uitverkochte producten -->
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100" style="border-left: 4px solid #dc3545;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fs-1 text-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div>
                    <div class="fs-2 fw-bold">
                        <?= (int)($statistieken['producten_uitverkocht'] ?? 0) ?>
                    </div>
                    <div class="text-muted small">Producten uitverkocht</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="#" class="btn btn-sm btn-outline-secondary w-100 disabled" tabindex="-1">
                    Producten <span class="badge bg-secondary ms-1">binnenkort</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Snelkoppelingen -->
<h5 class="text-muted mb-3">Snelkoppelingen</h5>
<div class="d-flex flex-wrap gap-2">
    <a href="/klanten/aanmaken" class="btn btn-success">
        <i class="bi bi-person-plus me-2"></i>Klant toevoegen
    </a>
    <a href="/klanten" class="btn btn-outline-primary">
        <i class="bi bi-people me-2"></i>Klantenlijst
    </a>
    <a href="/wachtwoord-wijzigen" class="btn btn-outline-secondary">
        <i class="bi bi-key me-2"></i>Wachtwoord wijzigen
    </a>
    <a href="#" class="btn btn-outline-secondary disabled" tabindex="-1">
        <i class="bi bi-box-seam me-2"></i>Producten (binnenkort)
    </a>
</div>
