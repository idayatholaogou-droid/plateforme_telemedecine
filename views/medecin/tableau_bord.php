<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="medecin-container">
    <h1>Bienvenue, Dr <?= htmlspecialchars($medecin['prenom']) ?></h1>

    <?php if ($medecin['statut'] !== 'valide'): ?>
        <div class="alerte alerte-erreur">
            Votre compte est actuellement <strong><?= htmlspecialchars($medecin['statut']) ?></strong>.
            Vous devez etre valide par un administrateur avant de pouvoir recevoir des rendez-vous.
        </div>
    <?php endif; ?>

    <div class="dashboard-cards">
        <a href="/medecin/disponibilites" class="dashboard-card">
            <h3>Mes disponibilites</h3>
            <p>Gerez vos creneaux de consultation.</p>
        </a>

        <a href="/rendezvous/liste" class="dashboard-card">
            <h3>Mon planning</h3>
            <p>Consultez et confirmez vos rendez-vous.</p>
        </a>

        <a href="/message/liste" class="dashboard-card">
            <h3>Messagerie</h3>
            <p>Echangez avec vos patients.</p>
        </a>

        <a href="/notification/liste" class="dashboard-card">
            <h3>Notifications</h3>
            <p>Consultez vos dernieres notifications.</p>
        </a>

        <a href="/medecin/profil" class="dashboard-card">
            <h3>Mon profil</h3>
            <p>Modifiez vos informations personnelles.</p>
        </a>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
