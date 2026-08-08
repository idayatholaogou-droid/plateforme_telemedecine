<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="consultation-container">
    <h1>Consultation du <?= htmlspecialchars($consultation['date_consultation']) ?></h1>

    <div class="consultation-infos">
        <p><strong>Patient :</strong> <?= htmlspecialchars($consultation['prenom_patient'] . ' ' . $consultation['nom_patient']) ?></p>
        <p><strong>Medecin :</strong> Dr <?= htmlspecialchars($consultation['prenom_medecin'] . ' ' . $consultation['nom_medecin']) ?></p>
    </div>

    <div class="consultation-details">
        <h3>Symptômes</h3>
        <p><?= nl2br(htmlspecialchars($consultation['symptomes'])) ?></p>

        <h3>Diagnostic</h3>
        <p><?= nl2br(htmlspecialchars($consultation['diagnostic'])) ?></p>

        <?php if (!empty($consultation['notes'])): ?>
            <h3>Notes</h3>
            <p><?= nl2br(htmlspecialchars($consultation['notes'])) ?></p>
        <?php endif; ?>
    </div>

    <?php if ($_SESSION['role'] === 'medecin'): ?>
        <a href="/ordonnance/creer?consultation=<?= $consultation['id_consultation'] ?>" class="btn btn-primary">
            Rédiger une ordonnance
        </a>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
