<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * ConversationModel
 * -------------------
 * Une conversation est liee a un rendez-vous (1:1), et regroupe les
 * messages echanges entre le patient et le medecin concernes.
 */

class ConversationModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Cree une conversation pour un rendez-vous (si elle n'existe pas deja)
     */
    public function creerPourRdv(int $idRdv): int
    {
        $existante = $this->trouverParRdv($idRdv);
        if ($existante) {
            return $existante['id_conversation'];
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO conversation (id_rdv, statut)
             VALUES (:id_rdv, 'active')
             RETURNING id_conversation"
        );
        $stmt->execute(['id_rdv' => $idRdv]);

        return $stmt->fetchColumn();
    }

    /**
     * Recupere la conversation liee a un rendez-vous
     */
    public function trouverParRdv(int $idRdv): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_conversation, id_rdv, date_creation, statut
             FROM conversation
             WHERE id_rdv = :id_rdv"
        );
        $stmt->execute(['id_rdv' => $idRdv]);

        return $stmt->fetch();
    }

    /**
     * Recupere une conversation par son id, avec les infos du rdv/patient/medecin
     */
    public function trouverParId(int $idConversation): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id_conversation, c.statut, rv.id_rdv, rv.id_patient, rv.id_medecin,
                    up.nom AS nom_patient, up.prenom AS prenom_patient,
                    um.nom AS nom_medecin, um.prenom AS prenom_medecin
             FROM conversation c
             JOIN rendez_vous rv ON rv.id_rdv = c.id_rdv
             JOIN utilisateur up ON up.id_utilisateur = rv.id_patient
             JOIN utilisateur um ON um.id_utilisateur = rv.id_medecin
             WHERE c.id_conversation = :id"
        );
        $stmt->execute(['id' => $idConversation]);

        return $stmt->fetch();
    }

    /**
     * Liste les conversations d'un utilisateur (patient ou medecin)
     */
    public function getParUtilisateur(int $idUtilisateur, string $role): array
    {
        $colonne = $role === 'patient' ? 'rv.id_patient' : 'rv.id_medecin';
        $colonneAutre = $role === 'patient'
            ? "um.nom AS nom_autre, um.prenom AS prenom_autre"
            : "up.nom AS nom_autre, up.prenom AS prenom_autre";

        $stmt = $this->pdo->prepare(
            "SELECT c.id_conversation, c.date_creation, c.statut, rv.id_rdv, rv.date_rdv,
                    $colonneAutre
             FROM conversation c
             JOIN rendez_vous rv ON rv.id_rdv = c.id_rdv
             JOIN utilisateur up ON up.id_utilisateur = rv.id_patient
             JOIN utilisateur um ON um.id_utilisateur = rv.id_medecin
             WHERE $colonne = :id_utilisateur
             ORDER BY c.date_creation DESC"
        );
        $stmt->execute(['id_utilisateur' => $idUtilisateur]);

        return $stmt->fetchAll();
    }
}
