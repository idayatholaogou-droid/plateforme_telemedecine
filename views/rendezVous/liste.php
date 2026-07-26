<?php

require_once __DIR__ . '/UtilisateurModel.php';

/**
 * MedecinModel
 * ------------
 * Herite de UtilisateurModel (seConnecter, emailExiste, modifierProfil...)
 * et ajoute les operations propres au medecin :
 * inscription, validation par l'admin, gestion des specialites.
 */

class MedecinModel extends UtilisateurModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Inscrit un nouveau medecin (2 tables : utilisateur + medecin)
     * Statut par defaut : 'en_attente' (doit etre valide par un admin)
     */
    public function inscrire(array $donnees): int
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Insertion dans la table mere "utilisateur"
            $stmtUser = $this->pdo->prepare(
                "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role)
                 VALUES (:nom, :prenom, :email, :mot_de_passe, 'medecin')
                 RETURNING id_utilisateur"
            );

            $stmtUser->execute([
                'nom'          => $donnees['nom'],
                'prenom'       => $donnees['prenom'],
                'email'        => $donnees['email'],
                'mot_de_passe' => password_hash($donnees['mot_de_passe'], PASSWORD_BCRYPT),
            ]);

            $idUtilisateur = $stmtUser->fetchColumn();

            // 2. Insertion dans la table fille "medecin"
            $stmtMedecin = $this->pdo->prepare(
                "INSERT INTO medecin (id_utilisateur, telephone, numero_licence, biographie, statut)
                 VALUES (:id_utilisateur, :telephone, :numero_licence, :biographie, 'en_attente')"
            );

            $stmtMedecin->execute([
                'id_utilisateur' => $idUtilisateur,
                'telephone'      => $donnees['telephone'] ?? null,
                'numero_licence' => $donnees['numero_licence'],
                'biographie'     => $donnees['biographie'] ?? null,
            ]);

            // 3. Association des specialites (table pivot medecin_specialite)
            if (!empty($donnees['specialites']) && is_array($donnees['specialites'])) {
                $stmtSpe = $this->pdo->prepare(
                    "INSERT INTO medecin_specialite (id_medecin, id_specialite)
                     VALUES (:id_medecin, :id_specialite)"
                );
                foreach ($donnees['specialites'] as $idSpecialite) {
                    $stmtSpe->execute([
                        'id_medecin'    => $idUtilisateur,
                        'id_specialite' => $idSpecialite,
                    ]);
                }
            }

            $this->pdo->commit();

            return $idUtilisateur;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Recupere un medecin complet (jointure utilisateur + medecin)
     */
    public function trouverParId(int $idUtilisateur): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.role,
                    m.telephone, m.numero_licence, m.biographie, m.statut, m.date_inscription
             FROM utilisateur u
             JOIN medecin m ON m.id_utilisateur = u.id_utilisateur
             WHERE u.id_utilisateur = :id"
        );
        $stmt->execute(['id' => $idUtilisateur]);

        return $stmt->fetch();
    }

    /**
     * Liste tous les medecins valides (visibles publiquement pour les patients)
     */
    public function getMedecinsValides(): array
    {
        $stmt = $this->pdo->query(
            "SELECT u.id_utilisateur, u.nom, u.prenom, m.biographie
             FROM utilisateur u
             JOIN medecin m ON m.id_utilisateur = u.id_utilisateur
             WHERE m.statut = 'valide'
             ORDER BY u.nom"
        );

        return $stmt->fetchAll();
    }

    /**
     * Liste les medecins en attente de validation (pour l'admin)
     */
    public function getMedecinsEnAttente(): array
    {
        $stmt = $this->pdo->query(
            "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, m.numero_licence, m.date_inscription
             FROM utilisateur u
             JOIN medecin m ON m.id_utilisateur = u.id_utilisateur
             WHERE m.statut = 'en_attente'
             ORDER BY m.date_inscription"
        );

        return $stmt->fetchAll();
    }

    /**
     * Recherche des medecins par specialite
     */
    public function rechercherParSpecialite(int $idSpecialite): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.id_utilisateur, u.nom, u.prenom, m.biographie
             FROM utilisateur u
             JOIN medecin m ON m.id_utilisateur = u.id_utilisateur
             JOIN medecin_specialite ms ON ms.id_medecin = u.id_utilisateur
             WHERE ms.id_specialite = :id_specialite AND m.statut = 'valide'
             ORDER BY u.nom"
        );
        $stmt->execute(['id_specialite' => $idSpecialite]);

        return $stmt->fetchAll();
    }

    /**
     * Valide le compte d'un medecin (action reservee a l'administrateur)
     */
    public function valider(int $idMedecin): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE medecin SET statut = 'valide' WHERE id_utilisateur = :id"
        );

        return $stmt->execute(['id' => $idMedecin]);
    }

    /**
     * Rejette le compte d'un medecin (action reservee a l'administrateur)
     */
    public function rejeter(int $idMedecin): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE medecin SET statut = 'rejete' WHERE id_utilisateur = :id"
        );

        return $stmt->execute(['id' => $idMedecin]);
    }

    /**
     * Met a jour les infos specifiques au medecin (telephone, biographie)
     */
    public function modifierInfosMedecin(int $idMedecin, string $telephone, string $biographie): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE medecin SET telephone = :telephone, biographie = :biographie
             WHERE id_utilisateur = :id"
        );

        return $stmt->execute([
            'telephone'  => $telephone,
            'biographie' => $biographie,
            'id'         => $idMedecin,
        ]);
    }
}
