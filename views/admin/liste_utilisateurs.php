<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="admin-container">
    <h1>Gestion des utilisateurs</h1>

    <?php if (empty($utilisateurs)): ?>
        <p>Aucun utilisateur enregistré.</p>
    <?php else: ?>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></td>
                        <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                        <td><span class="badge badge-<?= htmlspecialchars($utilisateur['role']) ?>">
                            <?= htmlspecialchars($utilisateur['role']) ?>
                        </span></td>
                        <td>
                            <?php if ((int) $utilisateur['id_utilisateur'] !== (int) $_SESSION['id_utilisateur']): ?>
                                <form action="/admin/supprimer-utilisateur" method="POST"
                                      onsubmit="return confirm('Confirmer la suppression de ce compte ?');"
                                      style="display:inline">
                                    <input type="hidden" name="id_utilisateur" value="<?= $utilisateur['id_utilisateur'] ?>">
                                    <button type="submit" class="btn btn-supprimer">Supprimer</button>
                                </form>
                            <?php else: ?>
                                <span class="texte-secondaire">(vous)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="/admin/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
