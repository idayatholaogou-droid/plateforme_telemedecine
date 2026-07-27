<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="rdv-container">
    <h1>Detail du rendez-vous</h1>

    <div class="rdv-details">
        <p>
            <strong><?= $_SESSION['role'] === 'patient' ? 'Medecin' : 'Patient' ?> :</strong>
            <?php if ($_SESSION['role'] === 'patient'): ?>
                Dr <?= htmlspecialchars($rdv['prenom_medecin'] . ' ' . $rdv['nom_medecin']) ?>
            <?php else: ?>
                <?= htmlspecialchars($rdv['prenom_patient'] . ' ' . $rdv['nom_patient']) ?>
            <?php endif; ?>
        </p>
        <p><strong>Date :</strong> <?= htmlspecialchars($rdv['date_rdv']) ?></p>
        <p><strong>Heure :</strong>
            <?= htmlspecialchars(substr($rdv['heure_debut'], 0, 5)) ?>
            - <?= htmlspecialchars(substr($rdv['heure_fin'], 0, 5)) ?>
        </p>
        <p><strong>Motif :</strong> <?= htmlspecialchars($rdv['motif']) ?></p>
        <p><strong>Statut :</strong>
            <span class="badge badge-<?= htmlspecialchars($rdv['statut']) ?>">
                <?= htmlspecialchars($rdv['statut']) ?>
            </span>
        </p>
    </div>

    <div class="rdv-actions">
        <a href="/message/conversation?rdv=<?= $rdv['id_rdv'] ?>" class="btn btn-primary">Envoyer un message</a>

        <?php if ($_SESSION['role'] === 'medecin' && $rdv['statut'] === 'en_attente'): ?>
            <form action="/rendezvous/confirmer" method="POST" style="display:inline">
                <input type="hidden" name="id_rdv" value="<?= $rdv['id_rdv'] ?>">
                <button type="submit" class="btn btn-valider">Confirmer</button>
            </form>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'medecin' && $rdv['statut'] === 'confirme'): ?>
            <a href="/consultation/creer?rdv=<?= $rdv['id_rdv'] ?>" class="btn btn-primary">Creer la consultation</a>
        <?php endif; ?>

        <?php if (in_array($rdv['statut'], ['en_attente', 'confirme'])): ?>
            <form action="/rendezvous/annuler" method="POST"
                  onsubmit="return confirm('Confirmer l\'annulation ?');"
                  style="display:inline">
                <input type="hidden" name="id_rdv" value="<?= $rdv['id_rdv'] ?>">
                <button type="submit" class="btn btn-rejeter">Annuler</button>
            </form>
        <?php endif; ?>
    </div>

    <a href="/rendezvous/liste" class="lien-retour">&larr; Retour a la liste</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
