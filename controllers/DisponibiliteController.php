<?php

require_once __DIR__ . '/../models/DisponibiliteModel.php';

class DisponibiliteController
{
    private DisponibiliteModel $disponibiliteModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->disponibiliteModel = new DisponibiliteModel();

        $this->verifierAcces();
    }

    private function verifierAcces(): void
    {
        if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'medecin') {
            header('Location: /connexion');
            exit;
        }
    }

    public function afficherListe(): void
    {
        $idMedecin = $_SESSION['id_utilisateur'];
        $creneaux = $this->disponibiliteModel->getCreneauxParMedecin($idMedecin);

        require __DIR__ . '/../views/medecin/disponibilites.php';
    }

    public function ajouter(): void
    {
        $idMedecin = $_SESSION['id_utilisateur'];
        $jour = $_POST['jour'] ?? '';
        $heureDebut = $_POST['heure_debut'] ?? '';
        $heureFin = $_POST['heure_fin'] ?? '';

        if ($jour === '' || $heureDebut === '' || $heureFin === '') {
            header('Location: /medecin/disponibilites?erreur=champs_manquants');
            exit;
        }

        if ($heureFin <= $heureDebut) {
            header('Location: /medecin/disponibilites?erreur=heure_invalide');
            exit;
        }

        $this->disponibiliteModel->ajouterCreneau($idMedecin, $jour, $heureDebut, $heureFin);

        header('Location: /medecin/disponibilites');
        exit;
    }

    public function supprimer(): void
    {
        $idMedecin = $_SESSION['id_utilisateur'];
        $idDispo = (int) ($_POST['id_dispo'] ?? 0);

        if ($idDispo > 0) {
            $supprime = $this->disponibiliteModel->supprimerCreneau($idDispo, $idMedecin);

            if (!$supprime) {
                header('Location: /medecin/disponibilites?erreur=creneau_reserve');
                exit;
            }
        }

        header('Location: /medecin/disponibilites');
        exit;
    }
}
