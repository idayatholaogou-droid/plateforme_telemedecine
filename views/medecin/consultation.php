<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="medecin-container">
    <h1>Rendez-vous a consulter</h1>
    <p>Rendez-vous confirmes pour lesquels vous pouvez rediger une consultation.</p>

    <?php if (empty($rendezVousAConsulter)): ?>
        <p>Aucun rendez-vous confirme en attente de consultation.</p>
    <?php else: ?>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Patient</th>
                    <th>Motif</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rendezVousAConsulter as $rdv): ?>
                    <tr>
                        <td><?= htmlspecialchars($rdv['date_rdv']) ?></td>
                        <td>
                            <?= htmlspecialchars(substr($rdv['heure_debut'], 0, 5)) ?>
                            - <?= htmlspecialchars(substr($rdv['heure_fin'], 0, 5)) ?>
                        </td>
                        <td><?= htmlspecialchars($rdv['prenom_patient'] . ' ' . $rdv['nom_patient']) ?></td>
                        <td><?= htmlspecialchars($rdv['motif']) ?></td>
                        <td>
                            <a href="/consultation/creer?rdv=<?= $rdv['id_rdv'] ?>" class="btn btn-primary">
                                Rediger la consultation
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="/medecin/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
