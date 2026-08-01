<?php

require_once __DIR__ . '/../models/MedecinModel.php';
require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../models/RendezVousModel.php';

/**
 * MedecinController
 * ------------------
 * Gere l'espace medecin : tableau de bord, profil, disponibilites,
 * consultation du dossier patient.
 * Toutes les methodes verifient que l'utilisateur connecte est bien medecin.
 */

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

    /**
     * Empeche l'acces aux pages medecin si l'utilisateur n'est pas
     * connecte ou n'a pas le role 'medecin'
     */
    private function verifierAcces(): void
    {
        if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'medecin') {
            header('Location: /connexion');
            exit;
        }
    }

    /**
     * Tableau de bord principal du medecin
     */
    public function tableauBord(): void
    {
        $medecin = $this->medecinModel->trouverParId($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/medecin/tableau_bord.php';
    }

    /**
     * Planning du medecin : ses rendez-vous confirmes ou en attente,
     * groupes par date pour une vue calendrier simplifiee
     */
    public function planning(): void
    {
        $idMedecin = $_SESSION['id_utilisateur'];
        $rendezVous = $this->rendezVousModel->getParMedecin($idMedecin);

        require __DIR__ . '/../views/medecin/planning.php';
    }

    /**
     * Liste les rendez-vous confirmes en attente de consultation
     * (point de depart pour le medecin avant de rediger une consultation)
     */
    public function listeAConsulter(): void
    {
        $idMedecin = $_SESSION['id_utilisateur'];
        $tousLesRdv = $this->rendezVousModel->getParMedecin($idMedecin);

        // Ne garde que les rendez-vous confirmes (pas encore consultes)
        $rendezVousAConsulter = array_filter(
            $tousLesRdv,
            fn($rdv) => $rdv['statut'] === 'confirme'
        );

        require __DIR__ . '/../views/medecin/consultation.php';
    }

    /**
     * Affiche le profil du medecin
     */
    public function afficherProfil(): void
    {
        $medecin = $this->medecinModel->trouverParId($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/medecin/profil.php';
    }

    /**
     * Traite la modification du profil (infos communes + infos medecin)
     */
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

    /**
     * Consulte le dossier complet d'un patient
     * Accessible uniquement si le medecin a (ou a eu) un rendez-vous avec lui
     */
    public function consulterDossierPatient(): void
    {
        $idPatient = (int) ($_GET['id'] ?? 0);
        $idMedecin = $_SESSION['id_utilisateur'];

        if ($idPatient <= 0) {
            http_response_code(404);
            echo "Patient introuvable.";
            return;
        }

        // Verification d'acces : ce medecin a-t-il deja eu un RDV avec ce patient ?
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
