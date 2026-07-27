<?php
/**
 * Partial alertes.php
 * ----------------------
 * A inclure dans n'importe quelle vue pour afficher un message
 * d'erreur ou de succes, sans dupliquer le HTML a chaque fois.
 *
 * Utilisation dans une vue :
 *   $erreur = "Message d'erreur";
 *   require __DIR__ . '/../partials/alertes.php';
 */
?>

<?php if (!empty($erreur)): ?>
    <div class="alerte alerte-erreur">
        <?= htmlspecialchars($erreur) ?>
    </div>
<?php endif; ?>

<?php if (!empty($messageSucces)): ?>
    <div class="alerte alerte-succes">
        <?= htmlspecialchars($messageSucces) ?>
    </div>
<?php endif; ?>
