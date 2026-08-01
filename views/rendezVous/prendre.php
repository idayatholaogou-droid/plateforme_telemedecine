<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="rdv-container">
    <h1>Prendre rendez-vous avec Dr <?= htmlspecialchars($medecin['prenom'] . ' ' . $medecin['nom']) ?></h1>

    <?php if (($_GET['erreur'] ?? '') === 'creneau_indisponible'): ?>
        <div class="alerte alerte-erreur">
            Ce creneau vient d'etre reserve par quelqu'un d'autre. Merci d'en choisir un autre.
        </div>
    <?php endif; ?>

    <?php if (empty($creneaux)): ?>
        <p>Aucun creneau disponible pour ce medecin actuellement.</p>
    <?php else: ?>
        <form action="/rendezvous/prendre" method="POST">
            <input type="hidden" name="id_medecin" value="<?= $medecin['id_utilisateur'] ?>">

            <div class="form-group">
                <label for="id_dispo">Choisissez un creneau</label>
                <select id="id_dispo" name="id_dispo" required>
                    <option value="">-- Selectionner --</option>
                    <?php foreach ($creneaux as $creneau): ?>
                        <option value="<?= $creneau['id_dispo'] ?>">
                            <?= htmlspecialchars($creneau['jour']) ?>
                            de <?= htmlspecialchars(substr($creneau['heure_debut'], 0, 5)) ?>
                            a <?= htmlspecialchars(substr($creneau['heure_fin'], 0, 5)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="motif">Motif de la consultation</label>
                <textarea id="motif" name="motif" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Confirmer la demande</button>
        </form>
    <?php endif; ?>

    <a href="/patient/rechercher-medecin" class="lien-retour">&larr; Retour a la recherche</a>
</div>

<script src="/assets/js/rendezVous.js"></script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
