<?php

require_once __DIR__ . '/../config/Database.php';



class DisponibiliteModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    
    public function ajouterCreneau(int $idMedecin, string $jour, string $heureDebut, string $heureFin): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO disponibilite (id_medecin, jour, heure_debut, heure_fin, statut)
             VALUES (:id_medecin, :jour, :heure_debut, :heure_fin, 'libre')
             RETURNING id_dispo"
        );

        $stmt->execute([
            'id_medecin'  => $idMedecin,
            'jour'        => $jour,
            'heure_debut' => $heureDebut,
            'heure_fin'   => $heureFin,
        ]);

        return $stmt->fetchColumn();
    }

    
    public function supprimerCreneau(int $idDispo, int $idMedecin): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM disponibilite
             WHERE id_dispo = :id_dispo AND id_medecin = :id_medecin AND statut = 'libre'"
        );

        $stmt->execute([
            'id_dispo'   => $idDispo,
            'id_medecin' => $idMedecin,
        ]);

        return $stmt->rowCount() > 0;
    }

    
    public function getCreneauxParMedecin(int $idMedecin): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_dispo, jour, heure_debut, heure_fin, statut
             FROM disponibilite
             WHERE id_medecin = :id_medecin
             ORDER BY jour, heure_debut"
        );
        $stmt->execute(['id_medecin' => $idMedecin]);

        return $stmt->fetchAll();
    }

    
    public function getCreneauxLibres(int $idMedecin): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_dispo, jour, heure_debut, heure_fin
             FROM disponibilite
             WHERE id_medecin = :id_medecin AND statut = 'libre' AND jour >= CURRENT_DATE
             ORDER BY jour, heure_debut"
        );
        $stmt->execute(['id_medecin' => $idMedecin]);

        return $stmt->fetchAll();
    }

    
    public function trouverParId(int $idDispo): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_dispo, id_medecin, jour, heure_debut, heure_fin, statut
             FROM disponibilite
             WHERE id_dispo = :id"
        );
        $stmt->execute(['id' => $idDispo]);

        return $stmt->fetch();
    }

   
    public function marquerReserve(int $idDispo): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE disponibilite SET statut = 'reserve'
             WHERE id_dispo = :id AND statut = 'libre'"
        );
        $stmt->execute(['id' => $idDispo]);

        return $stmt->rowCount() > 0;
    }

    
    public function libererCreneau(int $idDispo): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE disponibilite SET statut = 'libre' WHERE id_dispo = :id"
        );
        $stmt->execute(['id' => $idDispo]);

        return $stmt->rowCount() > 0;
    }
}
