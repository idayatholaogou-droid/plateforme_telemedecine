<?php

require_once __DIR__ . '/../config/Database.php';



class MessageModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    
    public function envoyer(int $idConversation, int $idExpediteur, string $contenu): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO message (id_conversation, id_expediteur, contenu, lu)
             VALUES (:id_conversation, :id_expediteur, :contenu, FALSE)
             RETURNING id_message"
        );

        $stmt->execute([
            'id_conversation' => $idConversation,
            'id_expediteur'   => $idExpediteur,
            'contenu'         => $contenu,
        ]);

        return $stmt->fetchColumn();
    }

    
    public function getParConversation(int $idConversation): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT m.id_message, m.contenu, m.date_envoi, m.lu, m.id_expediteur,
                    u.nom AS nom_expediteur, u.prenom AS prenom_expediteur
             FROM message m
             JOIN utilisateur u ON u.id_utilisateur = m.id_expediteur
             WHERE m.id_conversation = :id_conversation
             ORDER BY m.date_envoi ASC"
        );
        $stmt->execute(['id_conversation' => $idConversation]);

        return $stmt->fetchAll();
    }

    
    public function marquerCommeLus(int $idConversation, int $idUtilisateurCourant): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE message SET lu = TRUE
             WHERE id_conversation = :id_conversation AND id_expediteur != :id_utilisateur"
        );
        $stmt->execute([
            'id_conversation' => $idConversation,
            'id_utilisateur'  => $idUtilisateurCourant,
        ]);
    }

    
    public function compterNonLus(int $idUtilisateur): int
    {
        
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM message m
             JOIN conversation c ON c.id_conversation = m.id_conversation
             JOIN rendez_vous rv ON rv.id_rdv = c.id_rdv
             WHERE m.lu = FALSE
               AND m.id_expediteur != :id_expediteur
               AND (rv.id_patient = :id_patient OR rv.id_medecin = :id_medecin)"
        );
        $stmt->execute([
            'id_expediteur' => $idUtilisateur,
            'id_patient'    => $idUtilisateur,
            'id_medecin'    => $idUtilisateur,
        ]);

        return (int) $stmt->fetchColumn();
    }
}
