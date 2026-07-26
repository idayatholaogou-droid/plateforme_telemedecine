<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="admin-container">
    <h1>Statistiques de la plateforme</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-nombre"><?= (int) $statistiques['nb_patients'] ?></span>
            <span class="stat-label">Patients inscrits</span>
        </div>
        <div class="stat-card">
            <span class="stat-nombre"><?= (int) $statistiques['nb_medecins_valides'] ?></span>
            <span class="stat-label">Medecins valides</span>
        </div>
        <div class="stat-card stat-alerte">
            <span class="stat-nombre"><?= (int) $statistiques['nb_medecins_en_attente'] ?></span>
            <span class="stat-label">Medecins en attente</span>
        </div>
        <div class="stat-card">
            <span class="stat-nombre"><?= (int) $statistiques['nb_rendez_vous'] ?></span>
            <span class="stat-label">Rendez-vous total</span>
        </div>
        <div class="stat-card">
            <span class="stat-nombre"><?= (int) $statistiques['nb_consultations'] ?></span>
            <span class="stat-label">Consultations realisees</span>
        </div>
    </div>

    <div class="stats-notes">
        <p>
            Taux de consultations par rapport aux rendez-vous :
            <strong>
                <?php
                    $tauxConsultation = $statistiques['nb_rendez_vous'] > 0
                        ? round(($statistiques['nb_consultations'] / $statistiques['nb_rendez_vous']) * 100)
                        : 0;
                    echo $tauxConsultation;
                ?>%
            </strong>
        </p>
        <p>
            Total des utilisateurs medecins (valides + en attente) :
            <strong><?= (int) $statistiques['nb_medecins_valides'] + (int) $statistiques['nb_medecins_en_attente'] ?></strong>
        </p>
    </div>

    <a href="/admin/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
