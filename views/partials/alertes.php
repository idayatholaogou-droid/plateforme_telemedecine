<?php

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
