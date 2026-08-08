<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="consultation-container">
    <h1>Nouvelle consultation</h1>
    <p>Rendez-vous du <?= htmlspecialchars($rdv['date_rdv']) ?></p>

    <form action="/consultation/creer" method="POST">
        <input type="hidden" name="id_rdv" value="<?= $rdv['id_rdv'] ?>">

        <div class="form-group">
            <label for="symptomes">Symptômes rapportés</label>
            <textarea id="symptomes" name="symptomes" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="diagnostic">Diagnostic</label>
            <textarea id="diagnostic" name="diagnostic" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="notes">Notes complémentaires</label>
            <textarea id="notes" name="notes" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer la consultation</button>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
