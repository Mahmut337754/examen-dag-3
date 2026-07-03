<?php
$naam = htmlspecialchars(
    $klant['Voornaam']
    . ($klant['Tussenvoegsel'] ? ' ' . $klant['Tussenvoegsel'] : '')
    . ' ' . $klant['Achternaam'],
    ENT_QUOTES, 'UTF-8'
);
$errors          = $flash['errors'] ?? [];
$contactEmailErr = $errors['contact_email'] ?? '';
$straatnaamErr   = $errors['straatnaam']    ?? '';
$huisnummerErr   = $errors['huisnummer']    ?? '';
$postcodeErr     = $errors['postcode']      ?? '';
$plaatsErr       = $errors['plaats']        ?? '';
$mobielErr       = $errors['mobiel']        ?? '';
?>
<style>
    body { background:#f0f0f0; }
    .kw-bc { font-size:.84rem; margin-bottom:.5rem; color:#555; }
    .kw-bc a { color:#555; text-decoration:none; }
    .kw-bc a:hover { text-decoration:underline; }

    .kw-h1 { font-size:1.45rem; font-weight:700; margin-bottom:1.1rem; }
    .kw-h1 span { color:#c0392b; }

    /* Flash */
    .kw-flash {
        padding:.65rem 1rem; margin-bottom:.9rem;
        border-radius:.25rem; font-size:.84rem; max-width:680px;
    }
    .kw-flash.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

    /* Kaart */
    .kw-card {
        background:#fff; border:1px solid #ddd;
        border-radius:.35rem; padding:1.35rem 1.5rem; max-width:680px;
    }
    .kw-row { display:grid; grid-template-columns:1fr 1fr; gap:.85rem; margin-bottom:.9rem; }
    .kw-grp { display:flex; flex-direction:column; gap:.22rem; }
    .kw-grp.span2 { grid-column:span 2; }

    .kw-lbl { font-size:.81rem; font-weight:600; color:#333; }
    .kw-lbl .req { color:#c0392b; }

    .kw-input, .kw-textarea {
        border:1px solid #ced4da; border-radius:.25rem;
        padding:.4rem .65rem; font-size:.84rem;
        background:#fff; color:#333;
    }
    .kw-input:focus, .kw-textarea:focus { outline:none; border-color:#c0392b; }
    .kw-input.err, .kw-textarea.err { border-color:#dc3545; }
    .kw-input:disabled { background:#e9ecef; color:#6c757d; cursor:not-allowed; }
    .kw-textarea { resize:vertical; min-height:65px; }

    .kw-err { font-size:.74rem; color:#dc3545; margin-top:.1rem; }
    .kw-hint { font-size:.74rem; color:#6c757d; margin-top:.1rem; }

    /* Knoppen */
    .kw-actions { display:flex; justify-content:flex-end; gap:.5rem; margin-top:1.2rem; }
    .kw-btn-save {
        background:#c0392b; color:#fff; border:none;
        border-radius:.25rem; padding:.42rem 1.2rem;
        font-size:.84rem; font-weight:600; cursor:pointer;
    }
    .kw-btn-save:hover { background:#a93226; }
    .kw-btn-cancel {
        background:#6c757d; color:#fff; border:none;
        border-radius:.25rem; padding:.42rem 1rem;
        font-size:.84rem; font-weight:600; cursor:pointer;
        text-decoration:none; display:inline-block;
    }
    .kw-btn-cancel:hover { background:#5a6268; color:#fff; }
</style>

<!-- Breadcrumb -->
<div class="kw-bc">
    <a href="<?= url('/dashboard') ?>">Home</a> /
    <a href="<?= url('/klanten') ?>">Klanten</a> / Wijzigen
</div>

<!-- Titel -->
<h1 class="kw-h1"><span>Klant wijzigen</span> <?= $naam ?></h1>

<!-- Flash foutbericht -->
<?php if (!empty($flash) && $flash['type'] === 'error'): ?>
<div class="kw-flash error">
    <?= htmlspecialchars($flash['bericht'], ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<form method="post" action="<?= url('/klanten/wijzigen') ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id"         value="<?= (int)$klant['Id'] ?>">

    <div class="kw-card">

        <!-- Naam + Relatienummer -->
        <div class="kw-row">
            <div class="kw-grp">
                <label class="kw-lbl">Naam <span class="req">*</span></label>
                <input type="text" class="kw-input" value="<?= $naam ?>" disabled>
            </div>
            <div class="kw-grp">
                <label class="kw-lbl">Relatienummer</label>
                <input type="text" class="kw-input" value="<?= htmlspecialchars($klant['Relatienummer'], ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>
        </div>

        <!-- Contact e-mail + Account e-mail -->
        <div class="kw-row">
            <div class="kw-grp">
                <label class="kw-lbl" for="contact_email">Contact e-mail <span class="req">*</span></label>
                <input
                    type="email" id="contact_email" name="contact_email"
                    class="kw-input <?= $contactEmailErr ? 'err' : '' ?>"
                    value="<?= htmlspecialchars($klant['Email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($contactEmailErr): ?>
                    <div class="kw-err"><?= htmlspecialchars($contactEmailErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
            <div class="kw-grp">
                <label class="kw-lbl">Account e-mail</label>
                <input type="email" class="kw-input" value="<?= htmlspecialchars($klant['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>
        </div>

        <!-- Straatnaam + Huisnummer + Toevoeging -->
        <div class="kw-row">
            <div class="kw-grp">
                <label class="kw-lbl" for="straatnaam">Straatnaam <span class="req">*</span></label>
                <input
                    type="text" id="straatnaam" name="straatnaam"
                    class="kw-input <?= $straatnaamErr ? 'err' : '' ?>"
                    value="<?= htmlspecialchars($klant['Straatnaam'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($straatnaamErr): ?>
                    <div class="kw-err"><?= htmlspecialchars($straatnaamErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
            <div style="display:grid; grid-template-columns:2fr 1fr; gap:.6rem;">
                <div class="kw-grp">
                    <label class="kw-lbl" for="huisnummer">Huisnummer <span class="req">*</span></label>
                    <input
                        type="text" id="huisnummer" name="huisnummer"
                        class="kw-input <?= $huisnummerErr ? 'err' : '' ?>"
                        value="<?= htmlspecialchars($klant['Huisnummer'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                    <?php if ($huisnummerErr): ?>
                        <div class="kw-err"><?= htmlspecialchars($huisnummerErr, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="kw-grp">
                    <label class="kw-lbl" for="toevoeging">Toevoeging</label>
                    <input
                        type="text" id="toevoeging" name="toevoeging"
                        class="kw-input"
                        value="<?= htmlspecialchars($klant['Toevoeging'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            </div>
        </div>

        <!-- Postcode + Plaats -->
        <div class="kw-row">
            <div class="kw-grp">
                <label class="kw-lbl" for="postcode">Postcode <span class="req">*</span></label>
                <input
                    type="text" id="postcode" name="postcode"
                    class="kw-input <?= $postcodeErr ? 'err' : '' ?>"
                    value="<?= htmlspecialchars($klant['Postcode'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($postcodeErr): ?>
                    <div class="kw-err"><?= htmlspecialchars($postcodeErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
            <div class="kw-grp">
                <label class="kw-lbl" for="plaats">Plaats <span class="req">*</span></label>
                <input
                    type="text" id="plaats" name="plaats"
                    class="kw-input <?= $plaatsErr ? 'err' : '' ?>"
                    value="<?= htmlspecialchars($klant['Plaats'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($plaatsErr): ?>
                    <div class="kw-err"><?= htmlspecialchars($plaatsErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobiel -->
        <div class="kw-row">
            <div class="kw-grp">
                <label class="kw-lbl" for="mobiel">Mobiel <span class="req">*</span></label>
                <input
                    type="tel" id="mobiel" name="mobiel"
                    class="kw-input <?= $mobielErr ? 'err' : '' ?>"
                    value="<?= htmlspecialchars($klant['Mobiel'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
                <?php if ($mobielErr): ?>
                    <div class="kw-err"><?= htmlspecialchars($mobielErr, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bijzonderheden -->
        <div class="kw-grp span2" style="margin-bottom:.9rem;">
            <label class="kw-lbl" for="bijzonderheden">Bijzonderheden</label>
            <textarea id="bijzonderheden" name="bijzonderheden" class="kw-textarea"
            ><?= htmlspecialchars($klant['Bijzonderheden'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="kw-hint">Velden met een <span style="color:#c0392b">*</span> zijn verplicht.</div>

        <!-- Knoppen -->
        <div class="kw-actions">
            <button type="submit" class="kw-btn-save">Opslaan</button>
            <a href="<?= url('/klanten') ?>" class="kw-btn-cancel">Terug</a>
        </div>
    </div>
</form>

