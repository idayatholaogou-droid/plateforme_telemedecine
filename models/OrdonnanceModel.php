<?php

require_once __DIR__ . '/../config/Database.php';



class OrdonnanceModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    
    public function creer(int $idConsultation, array $medicaments): int|false
    {
        try {
            $this->pdo->beginTransaction();

            $stmtOrdo = $this->pdo->prepare(
                "INSERT INTO ordonnance (id_consultation)
                 VALUES (:id_consultation)
                 RETURNING id_ordonnance"
            );
            $stmtOrdo->execute(['id_consultation' => $idConsultation]);
            $idOrdonnance = $stmtOrdo->fetchColumn();

            $stmtLigne = $this->pdo->prepare(
                "INSERT INTO ligne_ordonnance (id_ordonnance, nom_medicament, posologie, duree, quantite)
                 VALUES (:id_ordonnance, :nom_medicament, :posologie, :duree, :quantite)"
            );

            foreach ($medicaments as $medicament) {
                $stmtLigne->execute([
                    'id_ordonnance'  => $idOrdonnance,
                    'nom_medicament' => $medicament['nom'],
                    'posologie'      => $medicament['posologie'],
                    'duree'          => $medicament['duree'] ?? null,
                    'quantite'       => $medicament['quantite'] ?? 1,
                ]);
            }

            $this->pdo->commit();

            return $idOrdonnance;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    
    public function trouverParId(int $idOrdonnance): array|false
    {
        $stmtOrdo = $this->pdo->prepare(
            "SELECT o.id_ordonnance, o.date_emission, o.id_consultation,
                    c.id_rdv, rv.id_patient, rv.id_medecin,
                    up.nom AS nom_patient, up.prenom AS prenom_patient,
                    um.nom AS nom_medecin, um.prenom AS prenom_medecin
             FROM ordonnance o
             JOIN consultation c ON c.id_consultation = o.id_consultation
             JOIN rendez_vous rv ON rv.id_rdv = c.id_rdv
             JOIN utilisateur up ON up.id_utilisateur = rv.id_patient
             JOIN utilisateur um ON um.id_utilisateur = rv.id_medecin
             WHERE o.id_ordonnance = :id"
        );
        $stmtOrdo->execute(['id' => $idOrdonnance]);
        $ordonnance = $stmtOrdo->fetch();

        if (!$ordonnance) {
            return false;
        }

        $stmtLignes = $this->pdo->prepare(
            "SELECT id_ligne, nom_medicament, posologie, duree, quantite
             FROM ligne_ordonnance
             WHERE id_ordonnance = :id_ordonnance"
        );
        $stmtLignes->execute(['id_ordonnance' => $idOrdonnance]);
        $ordonnance['medicaments'] = $stmtLignes->fetchAll();

        return $ordonnance;
    }

    
    public function getParConsultation(int $idConsultation): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_ordonnance, date_emission
             FROM ordonnance
             WHERE id_consultation = :id_consultation"
        );
        $stmt->execute(['id_consultation' => $idConsultation]);

        return $stmt->fetchAll();
    }

    
    public function getParPatient(int $idPatient): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT o.id_ordonnance, o.date_emission,
                    um.nom AS nom_medecin, um.prenom AS prenom_medecin
             FROM ordonnance o
             JOIN consultation c ON c.id_consultation = o.id_consultation
             JOIN rendez_vous rv ON rv.id_rdv = c.id_rdv
             JOIN utilisateur um ON um.id_utilisateur = rv.id_medecin
             WHERE rv.id_patient = :id_patient
             ORDER BY o.date_emission DESC"
        );
        $stmt->execute(['id_patient' => $idPatient]);

        return $stmt->fetchAll();
    }
}
