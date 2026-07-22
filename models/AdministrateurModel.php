<?php

require_once __DIR__ . '/UtilisateurModel.php';
require_once __DIR__ . '/MedecinModel.php';

/**
 * AdministrateurModel
 * -------------------
 * Herite de UtilisateurModel (seConnecter, emailExiste, modifierProfil...)
 * et ajoute les operations propres a l'administrateur :
 * inscription, validation/rejet des medecins, gestion des utilisateurs.
 *
 * La table "administrateur" n'a pas d'attributs propres : elle sert
 * uniquement a marquer qu'un utilisateur (id_utilisateur) a le role admin.
 */

class AdministrateurModel extends UtilisateurModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Inscrit un nouvel administrateur (2 tables : utilisateur + administrateur)
     */
    public function inscrire(array $donnees): int
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Insertion dans la table mere "utilisateur"
            $stmtUser = $this->pdo->prepare(
                "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role)
                 VALUES (:nom, :prenom, :email, :mot_de_passe, 'admin')
                 RETURNING id_utilisateur"
            );

            $stmtUser->execute([
                'nom'          => $donnees['nom'],
                'prenom'       => $donnees['prenom'],
                'email'        => $donnees['email'],
                'mot_de_passe' => password_hash($donnees['mot_de_passe'], PASSWORD_BCRYPT),
            ]);

            $idUtilisateur = $stmtUser->fetchColumn();

            // 2. Insertion dans la table fille "administrateur" (juste l'id)
            $stmtAdmin = $this->pdo->prepare(
                "INSERT INTO administrateur (id_utilisateur) VALUES (:id_utilisateur)"
            );
            $stmtAdmin->execute(['id_utilisateur' => $idUtilisateur]);

            $this->pdo->commit();

            return $idUtilisateur;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Recupere un administrateur (jointure utilisateur + administrateur)
     */
    public function trouverParId(int $idUtilisateur): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.role
             FROM utilisateur u
             JOIN administrateur a ON a.id_utilisateur = u.id_utilisateur
             WHERE u.id_utilisateur = :id"
        );
        $stmt->execute(['id' => $idUtilisateur]);

        return $stmt->fetch();
    }

    /**
     * Valide le compte d'un medecin
     */
    public function validerMedecin(int $idMedecin): bool
    {
        $medecinModel = new MedecinModel();
        return $medecinModel->valider($idMedecin);
    }

    /**
     * Rejette le compte d'un medecin
     */
    public function rejeterMedecin(int $idMedecin): bool
    {
        $medecinModel = new MedecinModel();
        return $medecinModel->rejeter($idMedecin);
    }

    /**
     * Liste tous les utilisateurs (tous roles confondus)
     */
    public function getTousUtilisateurs(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id_utilisateur, nom, prenom, email, role
             FROM utilisateur
             ORDER BY role, nom"
        );

        return $stmt->fetchAll();
    }

    /**
     * Supprime un compte utilisateur (patient, medecin ou admin)
     * Le ON DELETE CASCADE sur les tables filles s'occupe de la suppression
     * en cascade dans patient/medecin/administrateur
     */
    public function supprimerUtilisateur(int $idUtilisateur): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM utilisateur WHERE id_utilisateur = :id"
        );

        return $stmt->execute(['id' => $idUtilisateur]);
    }

    /**
     * Quelques statistiques globales pour le tableau de bord admin
     */
    public function getStatistiques(): array
    {
        $stmt = $this->pdo->query(
            "SELECT
                (SELECT COUNT(*) FROM patient) AS nb_patients,
                (SELECT COUNT(*) FROM medecin WHERE statut = 'valide') AS nb_medecins_valides,
                (SELECT COUNT(*) FROM medecin WHERE statut = 'en_attente') AS nb_medecins_en_attente,
                (SELECT COUNT(*) FROM rendez_vous) AS nb_rendez_vous,
                (SELECT COUNT(*) FROM consultation) AS nb_consultations"
        );

        return $stmt->fetch();
    }
}
