<?php

require_once __DIR__ . '/UtilisateurModel.php';

/**
 * PatientModel
 * ------------
 * Herite de UtilisateurModel (seConnecter, emailExiste, modifierProfil...)
 * et ajoute les operations propres au patient.
 */

class PatientModel extends UtilisateurModel
{
    public function __construct()
    {
        parent::__construct(); // initialise $this->pdo via UtilisateurModel
    }

    /**
     * Inscrit un nouveau patient (2 tables : utilisateur + patient)
     */
    public function inscrire(array $donnees): int
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Insertion dans la table mere "utilisateur"
            $stmtUser = $this->pdo->prepare(
                "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role)
                 VALUES (:nom, :prenom, :email, :mot_de_passe, 'patient')
                 RETURNING id_utilisateur"
            );

            $stmtUser->execute([
                'nom'          => $donnees['nom'],
                'prenom'       => $donnees['prenom'],
                'email'        => $donnees['email'],
                'mot_de_passe' => password_hash($donnees['mot_de_passe'], PASSWORD_BCRYPT),
            ]);

            $idUtilisateur = $stmtUser->fetchColumn();

            // 2. Insertion dans la table fille "patient" avec le meme id
            //    (telephone et date_inscription sont propres a patient)
            $stmtPatient = $this->pdo->prepare(
                "INSERT INTO patient (id_utilisateur, telephone, date_naissance, sexe, adresse, groupe_sanguin, antecedents_medicaux)
                 VALUES (:id_utilisateur, :telephone, :date_naissance, :sexe, :adresse, :groupe_sanguin, :antecedents)"
            );

            $stmtPatient->execute([
                'id_utilisateur'  => $idUtilisateur,
                'telephone'       => $donnees['telephone'] ?? null,
                'date_naissance'  => $donnees['date_naissance'] ?? null,
                'sexe'            => $donnees['sexe'] ?? null,
                'adresse'         => $donnees['adresse'] ?? null,
                'groupe_sanguin'  => $donnees['groupe_sanguin'] ?? null,
                'antecedents'     => $donnees['antecedents_medicaux'] ?? null,
            ]);

            $this->pdo->commit();

            return $idUtilisateur;
        } catch (PDOException $e) {
            // Annule les deux insertions si l'une des deux echoue
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Recupere un patient complet (jointure utilisateur + patient)
     */
    public function trouverParId(int $idUtilisateur): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.role,
                    p.telephone, p.date_naissance, p.sexe, p.adresse,
                    p.groupe_sanguin, p.antecedents_medicaux, p.date_inscription
             FROM utilisateur u
             JOIN patient p ON p.id_utilisateur = u.id_utilisateur
             WHERE u.id_utilisateur = :id"
        );
        $stmt->execute(['id' => $idUtilisateur]);

        return $stmt->fetch();
    }

    /**
     * Liste tous les patients
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, p.groupe_sanguin
             FROM utilisateur u
             JOIN patient p ON p.id_utilisateur = u.id_utilisateur
             ORDER BY u.nom"
        );

        return $stmt->fetchAll();
    }
}
