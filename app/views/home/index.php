<!-- ============================================================
     HERO
     ============================================================ -->
<section class="hero" id="home">
    <div class="container py-5">
        <div class="row align-items-center g-5">

            <!-- Tekst -->
            <div class="col-lg-7">
                <p class="text-uppercase fw-semibold mb-2" style="color:var(--salon-gold);letter-spacing:2px;">
                    Welkom bij
                </p>
                <h1 class="hero-title mb-3">Kniploket Tiko</h1>
                <p class="hero-sub mb-4">
                    Jouw kapsalon voor een frisse coupe, stralend haar en een ontspannen gevoel.
                    Maak eenvoudig online een afspraak of beheer je klantprofiel.
                </p>

                <div class="d-flex flex-wrap gap-3">
                    <!-- Klant: account aanmaken -->
                    <a href="/registreren"
                       class="btn btn-lg px-4 py-3"
                       style="background:var(--salon-gold);color:var(--salon-dark);font-weight:700;border-radius:.75rem;">
                        <i class="bi bi-person-plus-fill me-2"></i>Account aanmaken
                    </a>

                    <!-- Klant: inloggen -->
                    <a href="/login?type=klant"
                       class="btn btn-lg btn-outline-light px-4 py-3"
                       style="border-radius:.75rem;">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Inloggen als klant
                    </a>
                </div>

                <!-- Beheerdersingang (klein, discreet) -->
                <div class="mt-4">
                    <a href="/login" class="text-decoration-none" style="color:rgba(255,255,255,.4);font-size:.85rem;">
                        <i class="bi bi-shield-lock me-1"></i>Beheerpaneel voor medewerkers
                    </a>
                </div>
            </div>

            <!-- Icoon / illustratie -->
            <div class="col-lg-5 text-center d-none d-lg-block">
                <div class="scissors-icon mb-3">
                    <i class="bi bi-scissors"></i>
                </div>
                <div class="d-flex justify-content-center gap-4 mt-2">
                    <div class="text-center" style="color:var(--salon-accent);">
                        <div class="fs-2 fw-bold" style="color:var(--salon-gold);">10+</div>
                        <div class="small">jaar ervaring</div>
                    </div>
                    <div class="text-center" style="color:var(--salon-accent);">
                        <div class="fs-2 fw-bold" style="color:var(--salon-gold);">500+</div>
                        <div class="small">tevreden klanten</div>
                    </div>
                    <div class="text-center" style="color:var(--salon-accent);">
                        <div class="fs-2 fw-bold" style="color:var(--salon-gold);">5★</div>
                        <div class="small">beoordeling</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     PORTALEN: 2 kaarten
     ============================================================ -->
<section class="py-5 bg-white" id="portalen">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">Wat wil je doen?</h2>
        <p class="text-center text-muted mb-5">Kies hieronder je optie</p>

        <div class="row g-4 justify-content-center">

            <!-- Kaart: Nieuwe klant -->
            <div class="col-md-5">
                <div class="card card-portal shadow-sm h-100">
                    <div class="card-body p-5 text-center">
                        <div class="mb-3" style="font-size:3.5rem;color:var(--salon-gold);">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Nieuw bij Kniploket Tiko?</h4>
                        <p class="text-muted mb-4">
                            Maak gratis een account aan. Beheer je afspraken,
                            sla je voorkeuren op en geef allergieën door.
                        </p>
                        <a href="/registreren"
                           class="btn btn-lg w-100"
                           style="background:var(--salon-gold);color:var(--salon-dark);font-weight:700;">
                            <i class="bi bi-person-plus me-2"></i>Account aanmaken
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kaart: Bestaande klant -->
            <div class="col-md-5">
                <div class="card card-portal shadow-sm h-100">
                    <div class="card-body p-5 text-center">
                        <div class="mb-3" style="font-size:3.5rem;color:#0d6efd;">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Al een account?</h4>
                        <p class="text-muted mb-4">
                            Log in op je klantprofiel om je gegevens te bekijken
                            of je afspraken te beheren.
                        </p>
                        <a href="/login"
                           class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Inloggen
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     DIENSTEN
     ============================================================ -->
<section class="py-5 bg-light" id="diensten">
    <div class="container">
        <h2 class="text-center fw-bold mb-2">Onze diensten</h2>
        <p class="text-center text-muted mb-5">Professionele haarverzorging voor iedereen</p>

        <div class="row g-4 text-center">
            <?php
            $diensten = [
                ['bi-scissors',        'Knippen',    'Dames & heren knippen,<br>inclusief wassen en föhnen.'],
                ['bi-palette2',        'Kleuren',    'Permanente verf, highlights<br>en balayage technieken.'],
                ['bi-stars',           'Stylen',     'Föhnen, opsteken en<br>speciale gelegenheidskapsel.'],
                ['bi-magic',           'Extensions', 'Haar- en tape extensions<br>voor extra lengte of volume.'],
                ['bi-heart-fill',      'Verzorging', 'Dieptebehandeling, maskers<br>en hoofdhuidverzorging.'],
                ['bi-calendar-check',  'Afspraken',  'Eenvoudig online boeken,<br>24/7 beschikbaar.'],
            ];
            foreach ($diensten as [$icoon, $titel, $beschrijving]):
            ?>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mb-2" style="color:var(--salon-gold);">
                    <i class="bi <?= $icoon ?>"></i>
                </div>
                <h6 class="fw-semibold mb-1"><?= $titel ?></h6>
                <p class="text-muted small mb-0"><?= $beschrijving ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     CONTACT
     ============================================================ -->
<section class="py-5 bg-white" id="contact">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold mb-2">Vind ons</h2>
                <p class="text-muted mb-4">Wij zijn elke dag voor je klaar</p>

                <div class="row g-4">
                    <div class="col-md-4">
                        <i class="bi bi-geo-alt-fill fs-3 mb-2" style="color:var(--salon-gold);"></i>
                        <h6 class="fw-semibold">Adres</h6>
                        <p class="text-muted small mb-0">Kalverstraat 1<br>1012 NX Amsterdam</p>
                    </div>
                    <div class="col-md-4">
                        <i class="bi bi-clock-fill fs-3 mb-2" style="color:var(--salon-gold);"></i>
                        <h6 class="fw-semibold">Openingstijden</h6>
                        <p class="text-muted small mb-0">
                            Ma–Vr: 09:00–18:00<br>
                            Za: 09:00–17:00<br>
                            Zo: Gesloten
                        </p>
                    </div>
                    <div class="col-md-4">
                        <i class="bi bi-telephone-fill fs-3 mb-2" style="color:var(--salon-gold);"></i>
                        <h6 class="fw-semibold">Contact</h6>
                        <p class="text-muted small mb-0">
                            <a href="tel:+31201234567" class="text-decoration-none text-muted">020-123 4567</a><br>
                            <a href="mailto:info@kniploket.nl" class="text-decoration-none text-muted">info@kniploket.nl</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
