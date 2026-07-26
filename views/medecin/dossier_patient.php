<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="medecin-container">
    <h1>Dossier de <?= htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']) ?></h1>

    <div class="dossier-infos">
        <p><strong>Email :</strong> <?= htmlspecialchars($patient['email']) ?></p>
        <p><strong>Telephone :</strong> <?= htmlspecialchars($patient['telephone'] ?? 'Non renseigne') ?></p>
        <p><strong>Date de naissance :</strong> <?= htmlspecialchars($patient['date_naissance'] ?? 'Non renseignee') ?></p>
        <p><strong>Groupe sanguin :</strong> <?= htmlspecialchars($patient['groupe_sanguin'] ?? 'Non renseigne') ?></p>
        <p><strong>Antecedents medicaux :</strong></p>
        <p><?= nl2br(htmlspecialchars($patient['antecedents_medicaux'] ?? 'Aucun antecedent renseigne.')) ?></p>
    </div>

    <a href="/rendezvous/liste" class="lien-retour">&larr; Retour au planning</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
