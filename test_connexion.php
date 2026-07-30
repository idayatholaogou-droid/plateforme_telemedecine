<?php
require_once __DIR__ . '/config/Database.php';

try {
    $pdo = Database::getInstance();
    echo "Connexion reussie a la base PostgreSQL !";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}