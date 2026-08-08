<?php

require_once __DIR__ . '/../config/Database.php';



class UtilisateurModel
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function emailExiste(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM utilisateur WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

   
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

    
    public function seConnecter(string $email, string $motDePasse): array|false
    {
        $utilisateur = $this->trouverParEmail($email);

        if (!$utilisateur) {
            return false;
        }

        if (!password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            return false;
        }

      
        unset($utilisateur['mot_de_passe']);

        return $utilisateur;
    }

    
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
