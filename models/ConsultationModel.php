<?php

require_once __DIR__ . '/../config/Database.php';

class ConsultationModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function creer(int $idRdv, string $symptomes, string $diagnostic, string $notes = ''): int|false
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO consultation (id_rdv, symptomes, diagnostic, notes)
             VALUES (:id_rdv, :symptomes, :diagnostic, :notes)
             RETURNING id_consultation"
        );

        $stmt->execute([
            'id_rdv'     => $idRdv,
            'symptomes'  => $symptomes,
            'diagnostic' => $diagnostic,
            'notes'      => $notes,
        ]);

        return $stmt->fetchColumn();
    }

    public function trouverParId(int $idConsultation): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id_consultation, c.date_consultation, c.symptomes, c.diagnostic, c.notes,
                    rv.id_rdv, rv.date_rdv, rv.id_patient, rv.id_medecin,
                    up.nom AS nom_patient, up.prenom AS prenom_patient,
                    um.nom AS nom_medecin, um.prenom AS prenom_medecin
             FROM consultation c
             JOIN rendez_vous rv ON rv.id_rdv = c.id_rdv
             JOIN utilisateur up ON up.id_utilisateur = rv.id_patient
             JOIN utilisateur um ON um.id_utilisateur = rv.id_medecin
             WHERE c.id_consultation = :id"
        );
        $stmt->execute(['id' => $idConsultation]);

        return $stmt->fetch();
    }

    public function trouverParRdv(int $idRdv): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM consultation WHERE id_rdv = :id_rdv"
        );
        $stmt->execute(['id_rdv' => $idRdv]);

        return $stmt->fetch();
    }

    public function getHistoriqueParPatient(int $idPatient): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id_consultation, c.date_consultation, c.symptomes, c.diagnostic,
                    u.nom AS nom_medecin, u.prenom AS prenom_medecin
             FROM consultation c
             JOIN rendez_vous rv ON rv.id_rdv = c.id_rdv
             JOIN utilisateur u ON u.id_utilisateur = rv.id_medecin
             WHERE rv.id_patient = :id_patient
             ORDER BY c.date_consultation DESC"
        );
        $stmt->execute(['id_patient' => $idPatient]);

        return $stmt->fetchAll();
    }

    
    public function ajouterNotes(int $idConsultation, string $notes): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE consultation SET notes = :notes WHERE id_consultation = :id"
        );

        return $stmt->execute(['notes' => $notes, 'id' => $idConsultation]);
    }
}
