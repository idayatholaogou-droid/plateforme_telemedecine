<?php

require_once __DIR__ . '/../config/Database.php';



class DocumentModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    
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

    
    public function supprimer(int $idDocument, int $idPatient): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM document WHERE id_document = :id AND id_patient = :id_patient"
        );
        $stmt->execute(['id' => $idDocument, 'id_patient' => $idPatient]);

        return $stmt->rowCount() > 0;
    }
}
