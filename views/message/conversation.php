<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="conversation-container">
    <h1>
        Conversation avec
        <?php if ($_SESSION['role'] === 'patient'): ?>
            Dr <?= htmlspecialchars($conversation['prenom_medecin'] . ' ' . $conversation['nom_medecin']) ?>
        <?php else: ?>
            <?= htmlspecialchars($conversation['prenom_patient'] . ' ' . $conversation['nom_patient']) ?>
        <?php endif; ?>
    </h1>

    <div class="messages-liste">
        <?php if (empty($messages)): ?>
            <p>Aucun message pour le moment. Lancez la conversation !</p>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <?php $estMoi = (int) $message['id_expediteur'] === (int) $_SESSION['id_utilisateur']; ?>
                <div class="message-bulle <?= $estMoi ? 'message-moi' : 'message-autre' ?>">
                    <span class="message-auteur"><?= htmlspecialchars($message['prenom_expediteur']) ?></span>
                    <p><?= nl2br(htmlspecialchars($message['contenu'])) ?></p>
                    <span class="message-date"><?= htmlspecialchars($message['date_envoi']) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form action="/message/envoyer" method="POST" class="message-form">
        <input type="hidden" name="id_conversation" value="<?= $conversation['id_conversation'] ?>">
        <textarea name="contenu" rows="2" placeholder="Ecrire un message..." required></textarea>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>

<script src="/assets/js/message.js"></script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
