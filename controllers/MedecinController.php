<?php

require_once __DIR__ . '/../models/MedecinModel.php';
require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../models/RendezVousModel.php';


class MedecinController
{
    private MedecinModel $medecinModel;
    private PatientModel $patientModel;
    private RendezVousModel $rendezVousModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->medecinModel = new MedecinModel();
        $this->patientModel = new PatientModel();
        $this->rendezVousModel = new RendezVousModel();

        $this->verifierAcces();
    }

    private function verifierAcces(): void
    {
        if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'medecin') {
            header('Location: /connexion');
            exit;
        }
    }

    
    public function tableauBord(): void
    {
        $medecin = $this->medecinModel->trouverParId($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/medecin/tableau_bord.php';
    }

    public function planning(): void
    {
        $idMedecin = $_SESSION['id_utilisateur'];
        $rendezVous = $this->rendezVousModel->getParMedecin($idMedecin);

        require __DIR__ . '/../views/medecin/planning.php';
    }

    
    public function listeAConsulter(): void
    {
        $idMedecin = $_SESSION['id_utilisateur'];
        $tousLesRdv = $this->rendezVousModel->getParMedecin($idMedecin);

      
        $rendezVousAConsulter = array_filter(
            $tousLesRdv,
            fn($rdv) => $rdv['statut'] === 'confirme'
        );

        require __DIR__ . '/../views/medecin/consultation.php';
    }

    public function afficherProfil(): void
    {
        $medecin = $this->medecinModel->trouverParId($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/medecin/profil.php';
    }

    public function modifierProfil(): void
    {
        $idUtilisateur = $_SESSION['id_utilisateur'];

        $this->medecinModel->modifierProfil($idUtilisateur, [
            'nom'    => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
        ]);

        $this->medecinModel->modifierInfosMedecin(
            $idUtilisateur,
            trim($_POST['telephone'] ?? ''),
            trim($_POST['biographie'] ?? '')
        );

        header('Location: /medecin/profil');
        exit;
    }

    
    public function consulterDossierPatient(): void
    {
        $idPatient = (int) ($_GET['id'] ?? 0);
        $idMedecin = $_SESSION['id_utilisateur'];

        if ($idPatient <= 0) {
            http_response_code(404);
            echo "Patient introuvable.";
            return;
        }

       
        if (!$this->rendezVousModel->medecinAAccesPatient($idMedecin, $idPatient)) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $patient = $this->patientModel->trouverParId($idPatient);

        if (!$patient) {
            http_response_code(404);
            echo "Patient introuvable.";
            return;
        }

        require __DIR__ . '/../views/medecin/dossier_patient.php';
    }
}
