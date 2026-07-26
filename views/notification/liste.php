<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="notification-container">
    <h1>Mes notifications</h1>

    <?php if (empty($notifications)): ?>
        <p>Aucune notification pour le moment.</p>
    <?php else: ?>
        <div class="notifications-liste">
            <?php foreach ($notifications as $notif): ?>
                <div class="notification-item <?= $notif['lu'] ? '' : 'notification-non-lue' ?>">
                    <p><?= htmlspecialchars($notif['contenu']) ?></p>
                    <span class="notification-date"><?= htmlspecialchars($notif['date_creation']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
