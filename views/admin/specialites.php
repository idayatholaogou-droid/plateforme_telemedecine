<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="admin-container">
    <h1>Gestion des specialités</h1>

    <form action="/admin/specialites/ajouter" method="POST" class="specialite-form">
        <div class="form-row">
            <div class="form-group">
                <label for="libelle">Libellé</label>
                <input type="text" id="libelle" name="libelle" required placeholder="Ex: Cardiologie">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Description courte">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>

    <hr>

    <?php if (empty($specialites)): ?>
        <p>Aucune specialité enregistrée.</p>
    <?php else: ?>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($specialites as $specialite): ?>
                    <tr>
                        <td><?= htmlspecialchars($specialite['libelle']) ?></td>
                        <td><?= htmlspecialchars($specialite['description'] ?? '') ?></td>
                        <td>
                            <form action="/admin/specialites/supprimer" method="POST"
                                  onsubmit="return confirm('Supprimer cette specialite ?');"
                                  style="display:inline">
                                <input type="hidden" name="id_specialite" value="<?= $specialite['id_specialite'] ?>">
                                <button type="submit" class="btn btn-supprimer">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="/admin/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
