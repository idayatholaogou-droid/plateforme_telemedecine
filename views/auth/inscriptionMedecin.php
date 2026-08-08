<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="auth-container">
    <h1>Inscription Médecin</h1>

    <?php if (!empty($erreur)): ?>
        <div class="alerte alerte-erreur">
            <?= htmlspecialchars($erreur) ?>
        </div>
    <?php endif; ?>

    <form action="/inscription/medecin" method="POST">

        <div class="form-row">
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="prenom">Prenom *</label>
                <input type="text" id="prenom" name="prenom" required
                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe *</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="8">
            </div>

            <div class="form-group">
                <label for="telephone">Telephone</label>
                <input type="tel" id="telephone" name="telephone"
                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="numero_licence">Numero de licence medicale *</label>
            <input type="text" id="numero_licence" name="numero_licence" required
                   value="<?= htmlspecialchars($_POST['numero_licence'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Specialites</label>
            <?php if (empty($specialites)): ?>
                <p><em>Aucune specialité disponible pour le moment.</em></p>
            <?php else: ?>
                <div class="checkboxes-specialites">
                    <?php foreach ($specialites as $specialite): ?>
                        <label class="checkbox-item">
                            <input type="checkbox" name="specialites[]" value="<?= $specialite['id_specialite'] ?>">
                            <?= htmlspecialchars($specialite['libelle']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="biographie">Biographie / Présentation</label>
            <textarea id="biographie" name="biographie" rows="3"><?= htmlspecialchars($_POST['biographie'] ?? '') ?></textarea>
        </div>

        <div class="alerte alerte-info">
            Votre compte devra être validé par un administrateur avant de pouvoir recevoir des rendez-vous.
        </div>

        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>

    <p class="auth-lien">
        Deja un compte ? <a href="/connexion">Connectez-vous</a>.
    </p>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
