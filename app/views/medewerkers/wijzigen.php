<?php
$naam = htmlspecialchars(
    $medewerker['Voornaam']
    . ($medewerker['Tussenvoegsel'] ? ' ' . $medewerker['Tussenvoegsel'] : '')
    . ' ' . $medewerker['Achternaam'],
    ENT_QUOTES, 'UTF-8'
);

// Foutmeldingen
$errors           = $flash['errors'] ?? [];
$specialisatieErr = $errors['specialisatie'] ?? '';
$geboortedatumErr = $errors['geboortedatum'] ?? '';
$contactEmailErr  = $errors['contact_email'] ?? '';
$straatnaamErr    = $errors['straatnaam'] ?? '';
$huisnummerErr    = $errors['huisnummer'] ?? '';
$postcodeErr      = $errors['postcode'] ?? '';
$plaatsErr        = $errors['plaats'] ?? '';
$mobielErr        = $errors['mobiel'] ?? '';
?>
<style>
    /* ── Breadcrumb ── */
    .mw-bc { font-size:.85rem; margin-bottom:.6rem; }
    .mw-bc a { color:#c0392b; text-decoration:none; }
    .mw-bc a:hover { text-decoration:underline; }

    /* ── Titel ── */
    .mw-h1 { font-size:1.55rem; font-weight:700; margin-bottom:1.25rem; }
    .mw-h1 span { color:#c0392b; }

    /* ── Flash bericht ── */
    .mw-flash {
        padding:.7rem 1rem;
        margin-bottom:1rem;
        border-radius:.25rem;
        font-size:.85rem;
        max-width:680px;
    }
    .mw-flash.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

    /* ── Formulier card ── */
    .mw-card {
        background:#fff;
        border:1px solid #e0e0e0;
        border-radius:.4rem;
        padding:1.4rem 1.6rem;
        max-width:680px;
    }
    .mw-form-row {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:.9rem;
        margin-bottom:1rem;
    }
    .mw-form-group { display:flex; flex-direction:column; gap:.25rem; }
    .mw-form-group.full-width { grid-column:span 2; }

    .mw-lbl {
        font-size:.82rem;
        font-weight:600;
        color:#333;
    }
    .mw-lbl .req { color:#c0392b; }
    .mw-input, .mw-select {
        border:1px solid #ced4da;
        border-radius:.25rem;
        padding:.42rem .65rem;
        font-size:.85rem;
        background:#fff;
        color:#333;
    }
    .mw-input:focus, .mw-select:focus { outline:none; border-color:#c0392b; }
    .mw-input.error, .mw-select.error { border-color:#dc3545; }
    .mw-input:disabled { background:#e9ecef; cursor:not-allowed; }

    .mw-textarea {
        border:1px solid #ced4da;
        border-radius:.25rem;
        padding:.42rem .65rem;
        font-size:.85rem;
        resize:vertical;
        min-height:70px;
    }
    .mw-textarea:focus { outline:none; border-color:#c0392b; }

    .mw-error-msg {
        font-size:.75rem;
        color:#dc3545;
        margin-top:.15rem;
        line-height:1.3;
    }
    .mw-info {
        font-size:.75rem;
        color:#6c757d;
        margin-top:.15rem;
    }

    /* ── Knoppen ── */
    .mw-actions {
        display:flex;
        justify-content:flex-end;
        gap:.5rem;
        margin-top:1.3rem;
    }
    .mw-btn-save {
        background:#c0392b; color:#fff; border:none;
        border-radius:.25rem; padding:.42rem 1.2rem;
        font-size:.85rem; font-weight:600; cursor:pointer;
    }
    .mw-btn-save:hover { background:#a93226; }
    .mw-btn-cancel {
        background:#6c757d; color:#fff;
        border:none;
        border-radius:.25rem; padding:.42rem 1rem;
        font-size:.85rem; font-weight:600; cursor:pointer;
        text-decoration:none; display:inline-block;
    }
    .mw-btn-cancel:hover { background:#5a6268; color:#fff; }
</style>

<!-- Breadcrumb -->
<div class="mw-bc">
    <a href="<?= url('/dashboard') ?>">Home</a> /
    <a href="<?= url('/medewerkers') ?>">Medewerkers</a> /
    Wijzigen
</div>

<!-- Titel -->
<h1 class="mw-h1"><span>Medewerker wijzigen</span> <?= $naam ?></h1>

<!-- Flash bericht -->
<?php if ($flash && $flash['type'] === 'error'): ?>
<div class="mw-flash error">
    <?= htmlspecialchars($flash['bericht'], ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<!-- Formulier -->
<form method="post" action="<?= url('/medewerkers/wijzigen') ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id" value="<?= (int)$medewerker['Id'] ?>">

    <div class="mw-card">

        <!-- Rij 1: Naam + Specialisatie -->
        <div class="mw-form-row">
            <div class="mw-form-group">
                <label class="mw-lbl">Naam <span class="req">*</span></label>
                <input type="text" class="mw-input" value="<?= $naam ?>" disabled>
            </div>
            <div class="mw-form-group">
                <label class="mw-lbl" for="specialisatie">Specialisatie <span class="req">*</span></label>
                <select
                    id="specialisatie"
                    name="specialisatie"
                    class="mw-select <?= $specialisatieErr ? 'error' : '' ?>"
                    required
                >
                    <?php foreach ($specialisaties as $spec): ?>
                        <option value="<?= htmlspecialchars($spec, ENT_QUOTES, 'UTF-8') ?>"
                            <?= ($medewerker['Specialisatie'] === $spec) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($spec, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($specialisatieErr): ?>
                    <div class="mw-error-msg"><?= htmlspecialchars($specialisatieErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rij 2: Geboortedatum + Contact e-mail -->
        <div class="mw-form-row">
            <div class="mw-form-group">
                <label class="mw-lbl" for="geboortedatum">Geboortedatum <span class="req">*</span></label>
                <input
                    type="date"
                    id="geboortedatum"
                    name="geboortedatum"
                    class="mw-input <?= $geboortedatumErr ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($medewerker['Geboortedatum'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($geboortedatumErr): ?>
                    <div class="mw-error-msg"><?= htmlspecialchars($geboortedatumErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
            <div class="mw-form-group">
                <label class="mw-lbl" for="contact_email">Contact e-mail <span class="req">*</span></label>
                <input
                    type="email"
                    id="contact_email"
                    name="contact_email"
                    class="mw-input <?= $contactEmailErr ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($medewerker['ContactEmail'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($contactEmailErr): ?>
                    <div class="mw-error-msg"><?= htmlspecialchars($contactEmailErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rij 3: Account e-mail (disabled) + Straatnaam -->
        <div class="mw-form-row">
            <div class="mw-form-group">
                <label class="mw-lbl">Account e-mail</label>
                <input
                    type="email"
                    class="mw-input"
                    value="<?= htmlspecialchars($medewerker['AccountEmail'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    disabled
                >
            </div>
            <div class="mw-form-group">
                <label class="mw-lbl" for="straatnaam">Straatnaam <span class="req">*</span></label>
                <input
                    type="text"
                    id="straatnaam"
                    name="straatnaam"
                    class="mw-input <?= $straatnaamErr ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($medewerker['Straatnaam'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($straatnaamErr): ?>
                    <div class="mw-error-msg"><?= htmlspecialchars($straatnaamErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rij 4: Huisnummer + Toevoeging + Postcode -->
        <div class="mw-form-row">
            <div style="display:grid; grid-template-columns:2fr 1fr; gap:.6rem;">
                <div class="mw-form-group">
                    <label class="mw-lbl" for="huisnummer">Huisnummer <span class="req">*</span></label>
                    <input
                        type="text"
                        id="huisnummer"
                        name="huisnummer"
                        class="mw-input <?= $huisnummerErr ? 'error' : '' ?>"
                        value="<?= htmlspecialchars($medewerker['Huisnummer'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                    <?php if ($huisnummerErr): ?>
                        <div class="mw-error-msg"><?= htmlspecialchars($huisnummerErr, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="mw-form-group">
                    <label class="mw-lbl" for="toevoeging">Toevoeging</label>
                    <input
                        type="text"
                        id="toevoeging"
                        name="toevoeging"
                        class="mw-input"
                        value="<?= htmlspecialchars($medewerker['Toevoeging'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>
            <div class="mw-form-group">
                <label class="mw-lbl" for="postcode">Postcode <span class="req">*</span></label>
                <input
                    type="text"
                    id="postcode"
                    name="postcode"
                    class="mw-input <?= $postcodeErr ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($medewerker['Postcode'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($postcodeErr): ?>
                    <div class="mw-error-msg"><?= htmlspecialchars($postcodeErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rij 5: Plaats + Mobiel -->
        <div class="mw-form-row">
            <div class="mw-form-group">
                <label class="mw-lbl" for="plaats">Plaats <span class="req">*</span></label>
                <input
                    type="text"
                    id="plaats"
                    name="plaats"
                    class="mw-input <?= $plaatsErr ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($medewerker['Plaats'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($plaatsErr): ?>
                    <div class="mw-error-msg"><?= htmlspecialchars($plaatsErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
            <div class="mw-form-group">
                <label class="mw-lbl" for="mobiel">Mobiel <span class="req">*</span></label>
                <input
                    type="tel"
                    id="mobiel"
                    name="mobiel"
                    class="mw-input <?= $mobielErr ? 'error' : '' ?>"
                    value="<?= htmlspecialchars($medewerker['Mobiel'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($mobielErr): ?>
                    <div class="mw-error-msg"><?= htmlspecialchars($mobielErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rij 6: Opmerking -->
        <div class="mw-form-group full-width">
            <label class="mw-lbl" for="opmerking">Opmerking</label>
            <textarea
                id="opmerking"
                name="opmerking"
                class="mw-textarea"
            ><?= htmlspecialchars($medewerker['Opmerking'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            <div class="mw-info">Velden met een <span class="req">*</span> zijn verplicht.</div>
        </div>

        <!-- Knoppen -->
        <div class="mw-actions">
            <button type="submit" class="mw-btn-save">Opslaan</button>
            <a href="<?= url('/medewerkers/detail?id=' . (int)$medewerker['Id']) ?>" class="mw-btn-cancel">Terug</a>
        </div>
    </div>
</form>

