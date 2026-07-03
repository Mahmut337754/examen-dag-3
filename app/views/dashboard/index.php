<div style="padding: 2rem 0;">

    <!-- Welkom header -->
    <div class="mb-4">
        <h3 class="fw-semibold mb-1">Goedendag, <?= htmlspecialchars($gebruikerNaam, ENT_QUOTES, 'UTF-8') ?> 👋</h3>
        <p class="text-muted mb-0" style="font-size:.9rem;">
            <?= strftime('%A %d %B %Y') ?? date('d-m-Y') ?>
            &nbsp;·&nbsp; <?= htmlspecialchars(ucfirst($gebruikerRol), ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>

    <!-- Snelkoppelingen -->
    <div class="row g-3">

        <div class="col-sm-6 col-lg-3">
            <a href="<?= url('/klanten') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100" style="border-radius:.6rem;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.13)'" onmouseout="this.style.boxShadow=''">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div style="width:46px;height:46px;background:#fdecea;border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-people-fill" style="color:#c0392b;font-size:1.3rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark" style="font-size:.95rem;">Klanten</div>
                            <div class="text-muted" style="font-size:.78rem;">Beheer klantenlijst</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="<?= url('/medewerkers') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100" style="border-radius:.6rem;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.13)'" onmouseout="this.style.boxShadow=''">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div style="width:46px;height:46px;background:#eaf2fb;border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-person-badge-fill" style="color:#2471a3;font-size:1.3rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark" style="font-size:.95rem;">Medewerkers</div>
                            <div class="text-muted" style="font-size:.78rem;">Personeel overzicht</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="<?= url('/afspraken') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100" style="border-radius:.6rem;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.13)'" onmouseout="this.style.boxShadow=''">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div style="width:46px;height:46px;background:#eafaf1;border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-calendar-check-fill" style="color:#1e8449;font-size:1.3rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark" style="font-size:.95rem;">Afspraken</div>
                            <div class="text-muted" style="font-size:.78rem;">Planning bekijken</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="<?= url('/producten') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100" style="border-radius:.6rem;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.13)'" onmouseout="this.style.boxShadow=''">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div style="width:46px;height:46px;background:#fef9e7;border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-box-seam-fill" style="color:#d4ac0d;font-size:1.3rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark" style="font-size:.95rem;">Producten</div>
                            <div class="text-muted" style="font-size:.78rem;">Voorraad beheren</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>
