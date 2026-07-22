<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="auth-container">
    <h1>Connexion</h1>

    <?php if (!empty($erreur)): ?>
        <div class="alerte alerte-erreur">
            <?= htmlspecialchars($erreur) ?>
        </div>
    <?php endif; ?>

    <form action="/connexion" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <p class="auth-lien">
        Pas encore de compte ?
        <a href="/inscription/patient">Inscrivez-vous en tant que patient</a>
        ou
        <a href="/inscription/medecin">en tant que medecin</a>.
    </p>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
