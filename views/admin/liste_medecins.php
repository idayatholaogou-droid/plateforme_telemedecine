<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="admin-container">
    <h1>Medecins en attente de validation</h1>

    <?php if (empty($medecinsEnAttente)): ?>
        <p>Aucun medecin en attente pour le moment.</p>
    <?php else: ?>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>N° licence</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medecinsEnAttente as $medecin): ?>
                    <tr>
                        <td><?= htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']) ?></td>
                        <td><?= htmlspecialchars($medecin['email']) ?></td>
                        <td><?= htmlspecialchars($medecin['numero_licence']) ?></td>
                        <td><?= htmlspecialchars($medecin['date_inscription']) ?></td>
                        <td>
                            <form action="/admin/valider-medecin" method="POST" style="display:inline">
                                <input type="hidden" name="id_medecin" value="<?= $medecin['id_utilisateur'] ?>">
                                <button type="submit" class="btn btn-valider">Valider</button>
                            </form>
                            <form action="/admin/rejeter-medecin" method="POST" style="display:inline">
                                <input type="hidden" name="id_medecin" value="<?= $medecin['id_utilisateur'] ?>">
                                <button type="submit" class="btn btn-rejeter">Rejeter</button>
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
