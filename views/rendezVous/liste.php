<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="rdv-container">
    <h1>Mes rendez-vous</h1>

    <?php if (empty($rendezVous)): ?>
        <p>Aucun rendez-vous pour le moment.</p>
    <?php else: ?>
        <table class="table-admin">
            <thead>
                <tr>
                    <th><?= $_SESSION['role'] === 'patient' ? 'Medecin' : 'Patient' ?></th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rendezVous as $rdv): ?>
                    <tr>
                        <td>
                            <?php if ($_SESSION['role'] === 'patient'): ?>
                                Dr <?= htmlspecialchars($rdv['prenom_medecin'] . ' ' . $rdv['nom_medecin']) ?>
                            <?php else: ?>
                                <?= htmlspecialchars($rdv['prenom_patient'] . ' ' . $rdv['nom_patient']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($rdv['date_rdv']) ?></td>
                        <td>
                            <?= htmlspecialchars(substr($rdv['heure_debut'], 0, 5)) ?>
                            - <?= htmlspecialchars(substr($rdv['heure_fin'], 0, 5)) ?>
                        </td>
                        <td><?= htmlspecialchars($rdv['motif']) ?></td>
                        <td><span class="badge badge-<?= htmlspecialchars($rdv['statut']) ?>">
                            <?= htmlspecialchars($rdv['statut']) ?>
                        </span></td>
                        <td>
                            <?php if ($_SESSION['role'] === 'medecin'): ?>
                                <a href="/medecin/dossier-patient?id=<?= $rdv['id_patient'] ?? '' ?>" class="btn btn-secondaire">
                                    Voir dossier
                                </a>
                            <?php endif; ?>

                            <?php if ($rdv['statut'] === 'en_attente' && $_SESSION['role'] === 'medecin'): ?>
                                <form action="/rendezvous/confirmer" method="POST" style="display:inline">
                                    <input type="hidden" name="id_rdv" value="<?= $rdv['id_rdv'] ?>">
                                    <button type="submit" class="btn btn-valider">Confirmer</button>
                                </form>
                            <?php endif; ?>

                            <?php if (in_array($rdv['statut'], ['en_attente', 'confirme'])): ?>
                                <form action="/rendezvous/annuler" method="POST"
                                      onsubmit="return confirm('Confirmer l\'annulation ?');"
                                      style="display:inline">
                                    <input type="hidden" name="id_rdv" value="<?= $rdv['id_rdv'] ?>">
                                    <button type="submit" class="btn btn-rejeter">Annuler</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>