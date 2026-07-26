<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="admin-container">
    <h1>Liste des patients</h1>

    <?php if (empty($patients)): ?>
        <p>Aucun patient enregistre.</p>
    <?php else: ?>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Groupe sanguin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']) ?></td>
                        <td><?= htmlspecialchars($patient['email']) ?></td>
                        <td><?= htmlspecialchars($patient['groupe_sanguin'] ?? 'Non renseigne') ?></td>
                        <td>
                            <form action="/admin/supprimer-utilisateur" method="POST"
                                  onsubmit="return confirm('Confirmer la suppression de ce patient ?');"
                                  style="display:inline">
                                <input type="hidden" name="id_utilisateur" value="<?= $patient['id_utilisateur'] ?>">
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
