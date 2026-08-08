<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="patient-container">
    <h1>Mes documents</h1>

    <?php if (!empty($erreur)): ?>
        <div class="alerte alerte-erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form action="/patient/documents/upload" method="POST" enctype="multipart/form-data" class="upload-form">
        <div class="form-group">
            <label for="type">Type de document</label>
            <select id="type" name="type" required>
                <option value="ordonnance">Ordonnance scannée</option>
                <option value="resultat_analyse">Résultat d'analyse</option>
                <option value="certificat">Certificat medical</option>
                <option value="autre">Autre</option>
            </select>
        </div>

        <div class="form-group">
            <label for="fichier">Fichier (PDF, JPEG ou PNG, 5 Mo max)</label>
            <input type="file" id="fichier" name="fichier" accept=".pdf,.jpg,.jpeg,.png" required>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer le document</button>
    </form>

    <hr>

    <?php if (empty($documents)): ?>
        <p>Aucun document enregistre.</p>
    <?php else: ?>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Date d'ajout</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $document): ?>
                    <tr>
                        <td><?= htmlspecialchars($document['type']) ?></td>
                        <td><?= htmlspecialchars($document['date_upload']) ?></td>
                        <td>
                            <a href="/document/telecharger?id=<?= $document['id_document'] ?>" class="btn btn-primary">
                                Telecharger
                            </a>
                            <form action="/document/supprimer" method="POST"
                                  onsubmit="return confirm('Supprimer ce document ?');"
                                  style="display:inline">
                                <input type="hidden" name="id_document" value="<?= $document['id_document'] ?>">
                                <button type="submit" class="btn btn-supprimer">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="/patient/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>