<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="admin-container">
    <h1>Tableau de bord - Administrateur</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-nombre"><?= (int) $statistiques['nb_patients'] ?></span>
            <span class="stat-label">Patients</span>
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
            <span class="stat-label">Rendez-vous</span>
        </div>
        <div class="stat-card">
            <span class="stat-nombre"><?= (int) $statistiques['nb_consultations'] ?></span>
            <span class="stat-label">Consultations</span>
        </div>
    </div>

    <section class="admin-section">
        <div class="section-header">
            <h2>Medecins en attente de validation</h2>
            <a href="/admin/medecins-en-attente" class="lien-voir-tout">Voir tout</a>
        </div>

        <?php if (empty($medecinsEnAttente)): ?>
            <p>Aucun medecin en attente pour le moment.</p>
        <?php else: ?>
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>N° licence</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medecinsEnAttente as $medecin): ?>
                        <tr>
                            <td><?= htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']) ?></td>
                            <td><?= htmlspecialchars($medecin['email']) ?></td>
                            <td><?= htmlspecialchars($medecin['numero_licence']) ?></td>
                            <td>
                                <form action="/admin/valider-medecin" method="POST" style="display:inline">
                                    <input type="hidden" name="id_medecin" value="<?= $medecin['id_utilisateur'] ?>">
                                    <button type="submit" class="btn btn-valider">Valider</button>
                                </form>
                                <form action="/admin/rejeter-medecin" method="POST" style="display:inline">
                                    <input type="hidden" name="id_medecin" value="<?= $medecin['id_utilisateur'] ?>">
                                    <button type="submit" class="btn btn-rejeter">Rejeter</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
