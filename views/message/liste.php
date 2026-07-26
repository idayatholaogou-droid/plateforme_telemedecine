<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="conversation-container">
    <h1>Mes conversations</h1>

    <?php if (empty($conversations)): ?>
        <p>Aucune conversation pour le moment.</p>
    <?php else: ?>
        <div class="conversations-liste">
            <?php foreach ($conversations as $conv): ?>
                <a href="/message/conversation?rdv=<?= $conv['id_rdv'] ?>" class="conversation-item">
                    <strong><?= htmlspecialchars($conv['prenom_autre'] . ' ' . $conv['nom_autre']) ?></strong>
                    <span>Rendez-vous du <?= htmlspecialchars($conv['date_rdv']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
