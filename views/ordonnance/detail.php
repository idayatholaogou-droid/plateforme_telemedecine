<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="ordonnance-container">
    <h1>Ordonnance du <?= htmlspecialchars($ordonnance['date_emission']) ?></h1>

    <div class="ordonnance-infos">
        <p><strong>Patient :</strong> <?= htmlspecialchars($ordonnance['prenom_patient'] . ' ' . $ordonnance['nom_patient']) ?></p>
        <p><strong>Medecin :</strong> Dr <?= htmlspecialchars($ordonnance['prenom_medecin'] . ' ' . $ordonnance['nom_medecin']) ?></p>
    </div>

    <table class="table-admin">
        <thead>
            <tr>
                <th>Médicament</th>
                <th>Posologie</th>
                <th>Durée</th>
                <th>Quantité</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ordonnance['medicaments'] as $medicament): ?>
                <tr>
                    <td><?= htmlspecialchars($medicament['nom_medicament']) ?></td>
                    <td><?= htmlspecialchars($medicament['posologie']) ?></td>
                    <td><?= $medicament['duree'] ? htmlspecialchars($medicament['duree']) . ' jours' : '-' ?></td>
                    <td><?= htmlspecialchars($medicament['quantité']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button onclick="window.print()" class="btn btn-primary">Imprimer</button>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
