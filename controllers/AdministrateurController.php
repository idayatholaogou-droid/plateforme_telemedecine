<?php

require_once __DIR__ . '/../models/AdministrateurModel.php';
require_once __DIR__ . '/../models/MedecinModel.php';
require_once __DIR__ . '/../models/PatientModel.php';

class AdministrateurController
{
    private AdministrateurModel $administrateurModel;
    private MedecinModel $medecinModel;
    private PatientModel $patientModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->administrateurModel = new AdministrateurModel();
        $this->medecinModel = new MedecinModel();
        $this->patientModel = new PatientModel();

        $this->verifierAcces();
    }

    private function verifierAcces(): void
    {
        if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: /connexion');
            exit;
        }
    }

    public function tableauBord(): void
    {
        $statistiques = $this->administrateurModel->getStatistiques();
        $medecinsEnAttente = $this->medecinModel->getMedecinsEnAttente();

        require __DIR__ . '/../views/admin/tableau_bord.php';
    }

    public function listeMedecinsEnAttente(): void
    {
        $medecinsEnAttente = $this->medecinModel->getMedecinsEnAttente();

        require __DIR__ . '/../views/admin/liste_medecins.php';
    }

    
    public function validerMedecin(): void
    {
        $idMedecin = (int) ($_POST['id_medecin'] ?? 0);

        if ($idMedecin > 0) {
            $this->administrateurModel->validerMedecin($idMedecin);
        }

        header('Location: /admin/medecins-en-attente');
        exit;
    }

    public function rejeterMedecin(): void
    {
        $idMedecin = (int) ($_POST['id_medecin'] ?? 0);

        if ($idMedecin > 0) {
            $this->administrateurModel->rejeterMedecin($idMedecin);
        }

        header('Location: /admin/medecins-en-attente');
        exit;
    }

    public function listePatients(): void
    {
        $patients = $this->patientModel->getAll();

        require __DIR__ . '/../views/admin/liste_patients.php';
    }

    public function statistiques(): void
    {
        $statistiques = $this->administrateurModel->getStatistiques();

        require __DIR__ . '/../views/admin/statistiques.php';
    }

    public function listeUtilisateurs(): void
    {
        $utilisateurs = $this->administrateurModel->getTousUtilisateurs();

        require __DIR__ . '/../views/admin/liste_utilisateurs.php';
    }

    public function supprimerUtilisateur(): void
    {
        $idUtilisateur = (int) ($_POST['id_utilisateur'] ?? 0);

        if ($idUtilisateur > 0 && $idUtilisateur !== (int) $_SESSION['id_utilisateur']) {
            $this->administrateurModel->supprimerUtilisateur($idUtilisateur);
        }

        header('Location: /admin/utilisateurs');
        exit;
    }
}