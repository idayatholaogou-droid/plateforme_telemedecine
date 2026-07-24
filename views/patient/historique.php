<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="patient-container">
    <h1>Mon historique medical</h1>

    <?php if (empty($historique)): ?>
        <p>Aucune consultation enregistree pour le moment.</p>
    <?php else: ?>
        <div class="historique-liste">
            <?php foreach ($historique as $consultation): ?>
                <div class="historique-item">
                    <div class="historique-header">
                        <span class="historique-date"><?= htmlspecialchars($consultation['date_consultation']) ?></span>
                        <span class="historique-medecin">Dr <?= htmlspecialchars($consultation['prenom_medecin'] . ' ' . $consultation['nom_medecin']) ?></span>
                    </div>
                    <p><strong>Diagnostic :</strong> <?= htmlspecialchars($consultation['diagnostic']) ?></p>
                    <a href="/consultation/detail?id=<?= $consultation['id_consultation'] ?>" class="lien-voir-tout">
                        Voir le detail
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="/patient/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
