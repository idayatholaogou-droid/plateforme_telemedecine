<?php

require_once __DIR__ . '/../config/Database.php';

/**
 * DocumentModel
 * ---------------
 * Gere les documents lies a un patient (ordonnances scannees,
 * resultats d'analyses, certificats...).
 */

class DocumentModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Enregistre un document en base (le fichier physique est deja stocke
     * sur le disque par le controleur avant l'appel a cette methode)
     */
    public function ajouter(int $idPatient, string $type, string $cheminFichier, ?int $idConsultation = null): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO document (id_patient, id_consultation, type, chemin_fichier)
             VALUES (:id_patient, :id_consultation, :type, :chemin_fichier)
             RETURNING id_document"
        );

        $stmt->execute([
            'id_patient'      => $idPatient,
            'id_consultation' => $idConsultation,
            'type'            => $type,
            'chemin_fichier'  => $cheminFichier,
        ]);

        return $stmt->fetchColumn();
    }

    /**
     * Recupere un document par son id
     */
    public function trouverParId(int $idDocument): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_document, id_patient, id_consultation, type, chemin_fichier, date_upload
             FROM document
             WHERE id_document = :id"
        );
        $stmt->execute(['id' => $idDocument]);

        return $stmt->fetch();
    }

    /**
     * Liste tous les documents d'un patient (le plus recent d'abord)
     */
    public function getParPatient(int $idPatient): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_document, type, chemin_fichier, date_upload
             FROM document
             WHERE id_patient = :id_patient
             ORDER BY date_upload DESC"
        );
        $stmt->execute(['id_patient' => $idPatient]);

        return $stmt->fetchAll();
    }

    /**
     * Supprime un document (verifie que le patient en est bien le proprietaire)
     */
    public function supprimer(int $idDocument, int $idPatient): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM document WHERE id_document = :id AND id_patient = :id_patient"
        );
        $stmt->execute(['id' => $idDocument, 'id_patient' => $idPatient]);

        return $stmt->rowCount() > 0;
    }
}
