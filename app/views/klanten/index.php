<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-people me-2 text-primary"></i>Klanten</h1>
    <a href="<?= url('/klanten/aanmaken') ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Klant toevoegen
    </a>
</div>

<?php if (empty($klanten)): ?>
    <div class="alert alert-info">Nog geen klanten gevonden.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Relatienr.</th>
                    <th>Naam</th>
                    <th>E-mail</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($klanten as $k): ?>
                <tr>
                    <td><?= htmlspecialchars($k['Relatienummer'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?= htmlspecialchars(
                            $k['Voornaam']
                            . ($k['Tussenvoegsel'] ? ' ' . $k['Tussenvoegsel'] : '')
                            . ' ' . $k['Achternaam'],
                            ENT_QUOTES, 'UTF-8'
                        ) ?>
                    </td>
                    <td><?= htmlspecialchars($k['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a href="<?= url('/klanten/detail?id=' . (int)$k['Id']) ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= url('/klanten/wijzigen?id=' . (int)$k['Id']) ?>"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
