<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="medecin-container">
    <h1>Mes disponibilites</h1>

    <?php
        $erreursMessages = [
            'champs_manquants' => "Veuillez remplir tous les champs.",
            'heure_invalide'   => "L'heure de fin doit etre apres l'heure de debut.",
            'creneau_reserve'  => "Impossible de supprimer un creneau deja reserve.",
        ];
        $codeErreur = $_GET['erreur'] ?? null;
    ?>
    <?php if ($codeErreur && isset($erreursMessages[$codeErreur])): ?>
        <div class="alerte alerte-erreur"><?= htmlspecialchars($erreursMessages[$codeErreur]) ?></div>
    <?php endif; ?>

    <form action="/medecin/disponibilites/ajouter" method="POST" class="dispo-form">
        <div class="form-row">
            <div class="form-group">
                <label for="jour">Jour</label>
                <input type="date" id="jour" name="jour" required>
            </div>
            <div class="form-group">
                <label for="heure_debut">Heure debut</label>
                <input type="time" id="heure_debut" name="heure_debut" required>
            </div>
            <div class="form-group">
                <label for="heure_fin">Heure fin</label>
                <input type="time" id="heure_fin" name="heure_fin" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter le creneau</button>
    </form>

    <hr>

    <?php if (empty($creneaux)): ?>
        <p>Aucun creneau enregistre.</p>
    <?php else: ?>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Jour</th>
                    <th>Heure debut</th>
                    <th>Heure fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($creneaux as $creneau): ?>
                    <tr>
                        <td><?= htmlspecialchars($creneau['jour']) ?></td>
                        <td><?= htmlspecialchars(substr($creneau['heure_debut'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars(substr($creneau['heure_fin'], 0, 5)) ?></td>
                        <td><span class="badge badge-<?= htmlspecialchars($creneau['statut']) ?>">
                            <?= htmlspecialchars($creneau['statut']) ?>
                        </span></td>
                        <td>
                            <?php if ($creneau['statut'] === 'libre'): ?>
                                <form action="/medecin/disponibilites/supprimer" method="POST"
                                      onsubmit="return confirm('Supprimer ce creneau ?');"
                                      style="display:inline">
                                    <input type="hidden" name="id_dispo" value="<?= $creneau['id_dispo'] ?>">
                                    <button type="submit" class="btn btn-supprimer">Supprimer</button>
                                </form>
                            <?php else: ?>
                                <span class="texte-secondaire">Reserve</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="/medecin/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<script src="/assets/js/rendezVous.js"></script>
<?php require __DIR__ . '/../partials/footer.php'; ?>
