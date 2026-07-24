<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="patient-container">
    <h1>Rechercher un medecin</h1>

    <?php if (empty($medecins)): ?>
        <p>Aucun medecin trouve pour cette recherche.</p>
    <?php else: ?>
        <div class="medecins-grid">
            <?php foreach ($medecins as $medecin): ?>
                <div class="medecin-card">
                    <h3>Dr <?= htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']) ?></h3>
                    <?php if (!empty($medecin['biographie'])): ?>
                        <p><?= htmlspecialchars($medecin['biographie']) ?></p>
                    <?php endif; ?>
                    <a href="/rendezvous/prendre?medecin=<?= $medecin['id_utilisateur'] ?>" class="btn btn-primary">
                        Prendre rendez-vous
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="/patient/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
