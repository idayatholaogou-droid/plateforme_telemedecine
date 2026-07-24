<?php

require_once __DIR__ . '/../models/MedecinModel.php';
require_once __DIR__ . '/../models/PatientModel.php';

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

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->medecinModel = new MedecinModel();
        $this->patientModel = new PatientModel();

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
     * Affiche le profil du medecin
     */
    public function afficherProfil(): void
    {
        $medecin = $this->medecinModel->trouverParId($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/medecin/profil.php';
    }

    /**
     * Traite la modification du profil (infos communes uniquement pour l'instant)
     */
    public function modifierProfil(): void
    {
        $idUtilisateur = $_SESSION['id_utilisateur'];

        $this->medecinModel->modifierProfil($idUtilisateur, [
            'nom'    => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
        ]);

        // Infos specifiques (biographie, telephone) : a completer si tu
        // ajoutes une methode modifierInfosMedecin() dans MedecinModel

        header('Location: /medecin/profil');
        exit;
    }

    /**
     * Consulte le dossier complet d'un patient
     * Accessible uniquement si le medecin a (ou a eu) un rendez-vous avec lui,
     * a verifier via RendezVousModel une fois cree
     */
    public function consulterDossierPatient(): void
    {
        $idPatient = (int) ($_GET['id'] ?? 0);

        if ($idPatient <= 0) {
            http_response_code(404);
            echo "Patient introuvable.";
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
