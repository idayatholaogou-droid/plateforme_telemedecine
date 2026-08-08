<?php

/**
 * Classe Database (Singleton)
 * ----------------------------
 * Garantit une seule connexion PDO partagee dans toute l'application,
 * pour la base PostgreSQL "telemedecine".
 *
 * Utilisation :
 *      $pdo = Database::getInstance();
 *      $stmt = $pdo->query("SELECT * FROM utilisateur");
 */

class Database
{

    private static ?PDO $instance = null;

    private const HOST    = 'localhost';
    private const PORT    = '5433';
    private const DBNAME  = 'telemedecine';
    private const USER    = 'postgres';
    private const PASS    = '1412';

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s',
                    self::HOST,
                    self::PORT,
                    self::DBNAME
                );

                self::$instance = new PDO(
                    $dsn,
                    self::USER,
                    self::PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                die('Erreur de connexion a la base de donnees : ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
