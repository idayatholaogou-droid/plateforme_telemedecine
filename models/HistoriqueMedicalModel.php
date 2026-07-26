<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * HistoriqueMedicalModel
 * -------------------------
 * Gere les evenements de l'historique medical d'un patient
 * (allergies, maladies chroniques, interventions...).
 * Distinct des consultations : ce sont des informations que le patient
 * ou le medecin peuvent ajouter manuellement, hors du cadre d'un RDV precis.
 */

class HistoriqueMedicalModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Ajoute un evenement a l'historique medical d'un patient
     */
    public function ajouter(int $idPatient, string $typeEvenement, string $contenu): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO historique_medical (id_patient, type_evenement, contenu)
             VALUES (:id_patient, :type_evenement, :contenu)
             RETURNING id_historique"
        );

        $stmt->execute([
            'id_patient'     => $idPatient,
            'type_evenement' => $typeEvenement,
            'contenu'        => $contenu,
        ]);

        return $stmt->fetchColumn();
    }

    /**
     * Modifie un evenement existant (verifie que le patient en est proprietaire)
     */
    public function modifier(int $idHistorique, int $idPatient, string $typeEvenement, string $contenu): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE historique_medical
             SET type_evenement = :type_evenement, contenu = :contenu
             WHERE id_historique = :id AND id_patient = :id_patient"
        );

        $stmt->execute([
            'type_evenement' => $typeEvenement,
            'contenu'        => $contenu,
            'id'             => $idHistorique,
            'id_patient'     => $idPatient,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Supprime un evenement (verifie que le patient en est proprietaire)
     */
    public function supprimer(int $idHistorique, int $idPatient): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM historique_medical WHERE id_historique = :id AND id_patient = :id_patient"
        );
        $stmt->execute(['id' => $idHistorique, 'id_patient' => $idPatient]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Liste tout l'historique medical d'un patient (le plus recent d'abord)
     */
    public function getParPatient(int $idPatient): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_historique, type_evenement, contenu, date_creation
             FROM historique_medical
             WHERE id_patient = :id_patient
             ORDER BY date_creation DESC"
        );
        $stmt->execute(['id_patient' => $idPatient]);

        return $stmt->fetchAll();
    }
}
