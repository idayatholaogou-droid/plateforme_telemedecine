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
    // Contient l'unique instance PDO de l'application
    private static ?PDO $instance = null;

    // Parametres de connexion (a adapter selon ton environnement)
    private const HOST    = 'localhost';
    private const PORT    = '5433';
    private const DBNAME  = 'telemedecine';
    private const USER    = 'ton_utilisateur_postgres';
    private const PASS    = '1412';

    // Constructeur prive : empeche "new Database()" depuis l'exterieur
    private function __construct()
    {
    }

    // Empeche aussi le clonage de l'instance
    private function __clone()
    {
    }

    /**
     * Retourne l'unique instance PDO.
     * La cree seulement lors du premier appel, puis la reutilise.
     */
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
                // En production : logger l'erreur plutot que l'afficher
                die('Erreur de connexion a la base de donnees : ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
