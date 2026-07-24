<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="ordonnance-container">
    <h1>Rediger une ordonnance</h1>

    <?php if (!empty($erreur)): ?>
        <div class="alerte alerte-erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form action="/ordonnance/creer" method="POST">
        <input type="hidden" name="id_consultation" value="<?= $consultation['id_consultation'] ?>">

        <div id="lignes-medicaments">
            <div class="ligne-medicament">
                <input type="text" name="nom_medicament[]" placeholder="Nom du medicament" required>
                <input type="text" name="posologie[]" placeholder="Posologie (ex: 1 cp matin et soir)" required>
                <input type="number" name="duree[]" placeholder="Duree (jours)" min="1">
                <input type="number" name="quantite[]" placeholder="Quantite" min="1" value="1">
            </div>
        </div>

        <button type="button" id="ajouter-ligne" class="btn btn-secondaire">+ Ajouter un medicament</button>

        <br><br>
        <button type="submit" class="btn btn-primary">Enregistrer l'ordonnance</button>
    </form>
</div>

<script>
document.getElementById('ajouter-ligne').addEventListener('click', function () {
    const conteneur = document.getElementById('lignes-medicaments');
    const ligne = document.createElement('div');
    ligne.className = 'ligne-medicament';
    ligne.innerHTML = `
        <input type="text" name="nom_medicament[]" placeholder="Nom du medicament" required>
        <input type="text" name="posologie[]" placeholder="Posologie" required>
        <input type="number" name="duree[]" placeholder="Duree (jours)" min="1">
        <input type="number" name="quantite[]" placeholder="Quantite" min="1" value="1">
    `;
    conteneur.appendChild(ligne);
});
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
