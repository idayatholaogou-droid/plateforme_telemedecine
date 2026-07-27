<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telemedecine - Plateforme simplifiee</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <a href="/" class="logo">Telemedecine</a>
    <nav>
        <?php if (!empty($_SESSION['id_utilisateur'])): ?>
            <span>Bonjour, <?= htmlspecialchars($_SESSION['prenom']) ?></span>
            <a href="/deconnexion">Deconnexion</a>
        <?php else: ?>
            <a href="/connexion">Connexion</a>
            <a href="/inscription/patient">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<main class="site-content">

<?php if (!empty($_SESSION['role'])): ?>
    <?php if ($_SESSION['role'] === 'patient'): ?>
        <?php require __DIR__ . '/navbar_patient.php'; ?>
    <?php elseif ($_SESSION['role'] === 'medecin'): ?>
        <?php require __DIR__ . '/navbar_medecin.php'; ?>
    <?php elseif ($_SESSION['role'] === 'admin'): ?>
        <?php require __DIR__ . '/navbar_admin.php'; ?>
    <?php endif; ?>
<?php endif; ?>

