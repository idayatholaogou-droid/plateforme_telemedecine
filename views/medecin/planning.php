<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="medecin-container">
    <h1>Mon planning</h1>

    <?php if (empty($rendezVous)): ?>
        <p>Aucun rendez-vous pour le moment.</p>
    <?php else: ?>
        <?php
            $parDate = [];
            foreach ($rendezVous as $rdv) {
                $parDate[$rdv['date_rdv']][] = $rdv;
            }
        ?>

        <?php foreach ($parDate as $date => $rdvsDuJour): ?>
            <div class="planning-jour">
                <h3><?= htmlspecialchars($date) ?></h3>
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>Heure</th>
                            <th>Patient</th>
                            <th>Motif</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rdvsDuJour as $rdv): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars(substr($rdv['heure_debut'], 0, 5)) ?>
                                    - <?= htmlspecialchars(substr($rdv['heure_fin'], 0, 5)) ?>
                                </td>
                                <td><?= htmlspecialchars($rdv['prenom_patient'] . ' ' . $rdv['nom_patient']) ?></td>
                                <td><?= htmlspecialchars($rdv['motif']) ?></td>
                                <td><span class="badge badge-<?= htmlspecialchars($rdv['statut']) ?>">
                                    <?= htmlspecialchars($rdv['statut']) ?>
                                </span></td>
                                <td>
                                    <a href="/rendezvous/detail?id=<?= $rdv['id_rdv'] ?>" class="btn btn-secondaire">
                                        Voir detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/medecin/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
