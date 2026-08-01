<?php

require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../models/MedecinModel.php';

/**
 * PatientController
 * ------------------
 * Gere l'espace patient : tableau de bord, profil, recherche de medecin.
 * Toutes les methodes verifient que l'utilisateur connecte est bien patient.
 */

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

    /**
     * Empeche l'acces aux pages patient si l'utilisateur n'est pas
     * connecte ou n'a pas le role 'patient'
     */
    private function verifierAcces(): void
    {
        if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'patient') {
            header('Location: /connexion');
            exit;
        }
    }

    /**
     * Tableau de bord principal du patient
     */
    public function tableauBord(): void
    {
        $patient = $this->patientModel->trouverParId($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/patient/tableau_bord.php';
    }

    /**
     * Affiche le profil du patient
     */
    public function afficherProfil(): void
    {
        $patient = $this->patientModel->trouverParId($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/patient/profil.php';
    }

    /**
     * Traite la modification du profil (infos communes + infos patient)
     */
    public function modifierProfil(): void
    {
        $idUtilisateur = $_SESSION['id_utilisateur'];

        // Infos communes (table utilisateur)
        $this->patientModel->modifierProfil($idUtilisateur, [
            'nom'    => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
        ]);

        // Infos specifiques (table patient) : a completer si tu ajoutes
        // une methode modifierInfosPatient() dans PatientModel

        header('Location: /patient/profil');
        exit;
    }

    /**
     * Recherche de medecins par specialite (ou liste complete si aucun filtre)
     */
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
