<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="patient-container">
    <h1>Bienvenue, <?= htmlspecialchars($patient['prenom']) ?></h1>

    <div class="dashboard-cards">
        <a href="/patient/rechercher-medecin" class="dashboard-card">
            <h3>Rechercher un medecin</h3>
            <p>Trouvez un medecin par specialite et prenez rendez-vous.</p>
        </a>

        <a href="/rendezvous/liste" class="dashboard-card">
            <h3>Mes rendez-vous</h3>
            <p>Consultez vos rendez-vous a venir et passes.</p>
        </a>

        <a href="/patient/historique" class="dashboard-card">
            <h3>Historique medical</h3>
            <p>Retrouvez vos consultations precedentes.</p>
        </a>

        <a href="/patient/documents" class="dashboard-card">
            <h3>Mes documents</h3>
            <p>Ordonnances, resultats d'analyses et certificats.</p>
        </a>

        <a href="/patient/profil" class="dashboard-card">
            <h3>Mon profil</h3>
            <p>Modifiez vos informations personnelles.</p>
        </a>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
