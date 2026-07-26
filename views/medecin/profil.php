<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="medecin-container">
    <h1>Mon profil</h1>

    <form action="/medecin/modifier-profil" method="POST">

        <div class="form-row">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required
                       value="<?= htmlspecialchars($medecin['nom']) ?>">
            </div>

            <div class="form-group">
                <label for="prenom">Prenom</label>
                <input type="text" id="prenom" name="prenom" required
                       value="<?= htmlspecialchars($medecin['prenom']) ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" value="<?= htmlspecialchars($medecin['email']) ?>" disabled>
            <small>L'email ne peut pas etre modifie.</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="telephone">Telephone</label>
                <input type="tel" id="telephone" name="telephone"
                       value="<?= htmlspecialchars($medecin['telephone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>N° de licence</label>
                <input type="text" value="<?= htmlspecialchars($medecin['numero_licence']) ?>" disabled>
            </div>
        </div>

        <div class="form-group">
            <label for="biographie">Biographie</label>
            <textarea id="biographie" name="biographie" rows="4"><?= htmlspecialchars($medecin['biographie'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Statut du compte</label>
            <span class="badge badge-<?= htmlspecialchars($medecin['statut']) ?>">
                <?= htmlspecialchars($medecin['statut']) ?>
            </span>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>

    <a href="/medecin/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
