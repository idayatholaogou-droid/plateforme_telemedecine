<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * UtilisateurModel
 * ----------------
 * Gere les operations communes a tous les roles (patient, medecin, admin)
 * sur la table mere "utilisateur" : connexion, verification d'email, etc.
 */

class UtilisateurModel
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Verifie si un email existe deja (utile avant une inscription)
     */
    public function emailExiste(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM utilisateur WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Recupere un utilisateur (avec son role) a partir de son email
     */
    public function trouverParEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_utilisateur, nom, prenom, email, mot_de_passe, role
             FROM utilisateur
             WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);

        return $stmt->fetch();
    }

    /**
     * Recupere un utilisateur par son id
     */
    public function trouverParId(int $idUtilisateur): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_utilisateur, nom, prenom, email, role
             FROM utilisateur
             WHERE id_utilisateur = :id"
        );
        $stmt->execute(['id' => $idUtilisateur]);

        return $stmt->fetch();
    }

    /**
     * Verifie les identifiants de connexion.
     * Retourne les infos de l'utilisateur (sans le mot de passe) si valides,
     * sinon false.
     */
    public function seConnecter(string $email, string $motDePasse): array|false
    {
        $utilisateur = $this->trouverParEmail($email);

        if (!$utilisateur) {
            return false;
        }

        if (!password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            return false;
        }

        // On ne renvoie jamais le mot de passe hache au controleur/vue
        unset($utilisateur['mot_de_passe']);

        return $utilisateur;
    }

    /**
     * Change le mot de passe d'un utilisateur (apres verification de l'ancien)
     */
    public function changerMotDePasse(int $idUtilisateur, string $ancien, string $nouveau): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT mot_de_passe FROM utilisateur WHERE id_utilisateur = :id"
        );
        $stmt->execute(['id' => $idUtilisateur]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($ancien, $hash)) {
            return false;
        }

        $stmtMaj = $this->pdo->prepare(
            "UPDATE utilisateur SET mot_de_passe = :nouveau WHERE id_utilisateur = :id"
        );

        return $stmtMaj->execute([
            'nouveau' => password_hash($nouveau, PASSWORD_BCRYPT),
            'id'      => $idUtilisateur,
        ]);
    }

    /**
     * Met a jour les infos communes du profil (nom, prenom)
     */
    public function modifierProfil(int $idUtilisateur, array $donnees): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE utilisateur
             SET nom = :nom, prenom = :prenom
             WHERE id_utilisateur = :id"
        );

        return $stmt->execute([
            'nom'    => $donnees['nom'],
            'prenom' => $donnees['prenom'],
            'id'     => $idUtilisateur,
        ]);
    }
}
