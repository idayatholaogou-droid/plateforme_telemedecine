<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="patient-container">
    <h1>Mon profil</h1>

    <form action="/patient/modifier-profil" method="POST">

        <div class="form-row">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required
                       value="<?= htmlspecialchars($patient['nom']) ?>">
            </div>

            <div class="form-group">
                <label for="prenom">Prenom</label>
                <input type="text" id="prenom" name="prenom" required
                       value="<?= htmlspecialchars($patient['prenom']) ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" value="<?= htmlspecialchars($patient['email']) ?>" disabled>
            <small>L'email ne peut pas etre modifie.</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="telephone">Telephone</label>
                <input type="tel" id="telephone" name="telephone"
                       value="<?= htmlspecialchars($patient['telephone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse"
                       value="<?= htmlspecialchars($patient['adresse'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Date de naissance</label>
                <input type="text" value="<?= htmlspecialchars($patient['date_naissance'] ?? '') ?>" disabled>
            </div>

            <div class="form-group">
                <label>Groupe sanguin</label>
                <input type="text" value="<?= htmlspecialchars($patient['groupe_sanguin'] ?? 'Non renseigne') ?>" disabled>
            </div>
        </div>

        <div class="form-group">
            <label for="antecedents_medicaux">Antecedents medicaux</label>
            <textarea id="antecedents_medicaux" name="antecedents_medicaux" rows="3"><?= htmlspecialchars($patient['antecedents_medicaux'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>

    <a href="/patient/tableau-bord" class="lien-retour">&larr; Retour au tableau de bord</a>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
