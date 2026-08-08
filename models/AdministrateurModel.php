<?php

require_once __DIR__ . '/UtilisateurModel.php';
require_once __DIR__ . '/MedecinModel.php';


class AdministrateurModel extends UtilisateurModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function inscrire(array $donnees): int
    {
        try {
            $this->pdo->beginTransaction();

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

    public function validerMedecin(int $idMedecin): bool
    {
        $medecinModel = new MedecinModel();
        return $medecinModel->valider($idMedecin);
    }

    public function rejeterMedecin(int $idMedecin): bool
    {
        $medecinModel = new MedecinModel();
        return $medecinModel->rejeter($idMedecin);
    }

    public function getTousUtilisateurs(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id_utilisateur, nom, prenom, email, role
             FROM utilisateur
             ORDER BY role, nom"
        );

        return $stmt->fetchAll();
    }

    public function supprimerUtilisateur(int $idUtilisateur): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM utilisateur WHERE id_utilisateur = :id"
        );

        return $stmt->execute(['id' => $idUtilisateur]);
    }

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
  