<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * NotificationModel
 * --------------------
 * Gere les notifications envoyees aux utilisateurs (patient, medecin, admin).
 * Grace a la table mere "utilisateur", id_utilisateur suffit quel que soit le role.
 */

class NotificationModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Envoie (cree) une notification pour un utilisateur
     */
    public function envoyer(int $idUtilisateur, string $contenu): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO notification (id_utilisateur, contenu, lu)
             VALUES (:id_utilisateur, :contenu, FALSE)
             RETURNING id_notification"
        );

        $stmt->execute([
            'id_utilisateur' => $idUtilisateur,
            'contenu'        => $contenu,
        ]);

        return $stmt->fetchColumn();
    }

    /**
     * Liste toutes les notifications d'un utilisateur (les plus recentes d'abord)
     */
    public function getParUtilisateur(int $idUtilisateur): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_notification, contenu, date_creation, lu
             FROM notification
             WHERE id_utilisateur = :id_utilisateur
             ORDER BY date_creation DESC"
        );
        $stmt->execute(['id_utilisateur' => $idUtilisateur]);

        return $stmt->fetchAll();
    }

    /**
     * Compte les notifications non lues (pour affichage badge dans navbar)
     */
    public function compterNonLues(int $idUtilisateur): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM notification
             WHERE id_utilisateur = :id_utilisateur AND lu = FALSE"
        );
        $stmt->execute(['id_utilisateur' => $idUtilisateur]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Marque une notification precise comme lue
     */
    public function marquerCommeLue(int $idNotification, int $idUtilisateur): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE notification SET lu = TRUE
             WHERE id_notification = :id AND id_utilisateur = :id_utilisateur"
        );
        $stmt->execute(['id' => $idNotification, 'id_utilisateur' => $idUtilisateur]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Marque toutes les notifications d'un utilisateur comme lues
     */
    public function marquerToutesCommeLues(int $idUtilisateur): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE notification SET lu = TRUE
             WHERE id_utilisateur = :id_utilisateur AND lu = FALSE"
        );
        $stmt->execute(['id_utilisateur' => $idUtilisateur]);
    }
}
