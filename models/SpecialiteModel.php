<?php

require_once __DIR__ . '/../config/Database.php';


class SpecialiteModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

   
    public function ajouter(string $libelle, string $description = ''): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO specialite (libelle, description)
             VALUES (:libelle, :description)
             RETURNING id_specialite"
        );

        $stmt->execute([
            'libelle'     => $libelle,
            'description' => $description,
        ]);

        return $stmt->fetchColumn();
    }

    public function modifier(int $idSpecialite, string $libelle, string $description = ''): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE specialite SET libelle = :libelle, description = :description
             WHERE id_specialite = :id"
        );

        return $stmt->execute([
            'libelle'     => $libelle,
            'description' => $description,
            'id'          => $idSpecialite,
        ]);
    }

    
    public function supprimer(int $idSpecialite): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM specialite WHERE id_specialite = :id"
        );
        $stmt->execute(['id' => $idSpecialite]);

        return $stmt->rowCount() > 0;
    }

    
    public function trouverParId(int $idSpecialite): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_specialite, libelle, description FROM specialite WHERE id_specialite = :id"
        );
        $stmt->execute(['id' => $idSpecialite]);

        return $stmt->fetch();
    }

    
    public function getToutes(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id_specialite, libelle, description FROM specialite ORDER BY libelle"
        );

        return $stmt->fetchAll();
    }
}
