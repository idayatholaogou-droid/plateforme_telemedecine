<?php

require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../models/MedecinModel.php';


class PatientController
{
    private PatientModel $patientModel;
    private MedecinModel $medecinModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->patientModel = new PatientModel();
        $this->medecinModel = new MedecinModel();

        $this->verifierAcces();
    }

    private function verifierAcces(): void
    {
        if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'patient') {
            header('Location: /connexion');
            exit;
        }
    }

    public function tableauBord(): void
    {
        $patient = $this->patientModel->trouverParId($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/patient/tableau_bord.php';
    }

    
    public function afficherProfil(): void
    {
        $patient = $this->patientModel->trouverParId($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/patient/profil.php';
    }

    
    public function modifierProfil(): void
    {
        $idUtilisateur = $_SESSION['id_utilisateur'];

        $this->patientModel->modifierProfil($idUtilisateur, [
            'nom'    => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
        ]);

       
        header('Location: /patient/profil');
        exit;
    }

    public function rechercherMedecin(): void
    {
        $idSpecialite = $_GET['specialite'] ?? null;

        if ($idSpecialite) {
            $medecins = $this->medecinModel->rechercherParSpecialite((int) $idSpecialite);
        } else {
            $medecins = $this->medecinModel->getMedecinsValides();
        }

        require __DIR__ . '/../views/patient/rechercher_medecin.php';
    }
}
