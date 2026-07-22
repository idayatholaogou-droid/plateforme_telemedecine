<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="auth-container">
    <h1>Inscription Patient</h1>

    <?php if (!empty($erreur)): ?>
        <div class="alerte alerte-erreur">
            <?= htmlspecialchars($erreur) ?>
        </div>
    <?php endif; ?>

    <form action="/inscription/patient" method="POST">

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

        <div class="form-row">
            <div class="form-group">
                <label for="date_naissance">Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance"
                       value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="sexe">Sexe</label>
                <select id="sexe" name="sexe">
                    <option value="">-- Choisir --</option>
                    <option value="M">Masculin</option>
                    <option value="F">Feminin</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="adresse">Adresse</label>
            <input type="text" id="adresse" name="adresse"
                   value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="groupe_sanguin">Groupe sanguin</label>
                <select id="groupe_sanguin" name="groupe_sanguin">
                    <option value="">-- Inconnu --</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="antecedents_medicaux">Antecedents medicaux</label>
            <textarea id="antecedents_medicaux" name="antecedents_medicaux" rows="3"><?= htmlspecialchars($_POST['antecedents_medicaux'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>

    <p class="auth-lien">
        Deja un compte ? <a href="/connexion">Connectez-vous</a>.
    </p>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
