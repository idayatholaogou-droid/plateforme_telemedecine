<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/DisponibiliteModel.php';

/**
 * RendezVousModel
 * -----------------
 * Gere le cycle de vie complet d'un rendez-vous : creation, confirmation,
 * annulation. Travaille en transaction avec DisponibiliteModel puisque
 * prendre un RDV reserve toujours un creneau en meme temps.
 */

class RendezVousModel
{
    private PDO $pdo;
    private DisponibiliteModel $disponibiliteModel;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
        $this->disponibiliteModel = new DisponibiliteModel();
    }

    /**
     * Cree un rendez-vous : reserve le creneau + insere la ligne rendez_vous
     * dans une seule transaction (les deux doivent reussir ensemble)
     */
    public function creer(int $idPatient, int $idMedecin, int $idDispo, string $motif = ''): int|false
    {
        $dispo = $this->disponibiliteModel->trouverParId($idDispo);

        if (!$dispo || $dispo['statut'] !== 'libre' || (int) $dispo['id_medecin'] !== $idMedecin) {
            return false; // creneau invalide, deja pris, ou n'appartient pas a ce medecin
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Marque le creneau comme reserve
            $reserve = $this->disponibiliteModel->marquerReserve($idDispo);
            if (!$reserve) {
                // Quelqu'un d'autre a reserve entre-temps (cas rare mais possible)
                $this->pdo->rollBack();
                return false;
            }

            // 2. Cree le rendez-vous
            $stmt = $this->pdo->prepare(
                "INSERT INTO rendez_vous (id_patient, id_medecin, id_dispo, date_rdv, motif, statut)
                 VALUES (:id_patient, :id_medecin, :id_dispo, :date_rdv, :motif, 'en_attente')
                 RETURNING id_rdv"
            );
            $stmt->execute([
                'id_patient' => $idPatient,
                'id_medecin' => $idMedecin,
                'id_dispo'   => $idDispo,
                'date_rdv'   => $dispo['jour'],
                'motif'      => $motif,
            ]);

            $idRdv = $stmt->fetchColumn();

            $this->pdo->commit();

            return $idRdv;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Annule un rendez-vous : libere le creneau + change le statut
     */
    public function annuler(int $idRdv): bool
    {
        $rdv = $this->trouverParId($idRdv);

        if (!$rdv || $rdv['statut'] === 'annule') {
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "UPDATE rendez_vous SET statut = 'annule' WHERE id_rdv = :id"
            );
            $stmt->execute(['id' => $idRdv]);

            $this->disponibiliteModel->libererCreneau($rdv['id_dispo']);

            $this->pdo->commit();

            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Confirme un rendez-vous (action du medecin)
     */
    public function confirmer(int $idRdv, int $idMedecin): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE rendez_vous SET statut = 'confirme'
             WHERE id_rdv = :id AND id_medecin = :id_medecin AND statut = 'en_attente'"
        );
        $stmt->execute(['id' => $idRdv, 'id_medecin' => $idMedecin]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Marque un rendez-vous comme termine (apres la consultation)
     */
    public function terminer(int $idRdv): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE rendez_vous SET statut = 'termine' WHERE id_rdv = :id"
        );
        $stmt->execute(['id' => $idRdv]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Recupere un rendez-vous par son id
     */
    public function trouverParId(int $idRdv): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM rendez_vous WHERE id_rdv = :id"
        );
        $stmt->execute(['id' => $idRdv]);

        return $stmt->fetch();
    }

    /**
     * Liste les rendez-vous d'un patient (avec infos du medecin)
     */
    public function getParPatient(int $idPatient): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT rv.id_rdv, rv.date_rdv, rv.motif, rv.statut,
                    u.nom AS nom_medecin, u.prenom AS prenom_medecin,
                    d.heure_debut, d.heure_fin
             FROM rendez_vous rv
             JOIN utilisateur u ON u.id_utilisateur = rv.id_medecin
             JOIN disponibilite d ON d.id_dispo = rv.id_dispo
             WHERE rv.id_patient = :id_patient
             ORDER BY rv.date_rdv DESC"
        );
        $stmt->execute(['id_patient' => $idPatient]);

        return $stmt->fetchAll();
    }

    /**
     * Liste les rendez-vous d'un medecin (avec infos du patient) = son planning
     */
    public function getParMedecin(int $idMedecin): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT rv.id_rdv, rv.id_patient, rv.date_rdv, rv.motif, rv.statut,
                    u.nom AS nom_patient, u.prenom AS prenom_patient,
                    d.heure_debut, d.heure_fin
             FROM rendez_vous rv
             JOIN utilisateur u ON u.id_utilisateur = rv.id_patient
             JOIN disponibilite d ON d.id_dispo = rv.id_dispo
             WHERE rv.id_medecin = :id_medecin
             ORDER BY rv.date_rdv DESC"
        );
        $stmt->execute(['id_medecin' => $idMedecin]);

        return $stmt->fetchAll();
    }

    /**
     * Verifie si un medecin a deja eu un rendez-vous avec un patient donne
     * (utile pour securiser l'acces au dossier patient)
     */
    public function medecinAAccesPatient(int $idMedecin, int $idPatient): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM rendez_vous
             WHERE id_medecin = :id_medecin AND id_patient = :id_patient
             LIMIT 1"
        );
        $stmt->execute(['id_medecin' => $idMedecin, 'id_patient' => $idPatient]);

        return (bool) $stmt->fetchColumn();
    }
}
