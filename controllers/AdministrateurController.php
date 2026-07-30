<?php

require_once __DIR__ . '/../models/AdministrateurModel.php';
require_once __DIR__ . '/../models/MedecinModel.php';
require_once __DIR__ . '/../models/PatientModel.php';

/**
 * AdministrateurController
 * -------------------------
 * Gere le tableau de bord admin : validation des medecins,
 * gestion des utilisateurs, statistiques.
 * Toutes les methodes verifient que l'utilisateur connecte est bien admin.
 */

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

    /**
     * Empeche l'acces a toutes les pages admin si l'utilisateur
     * n'est pas connecte ou n'a pas le role 'admin'
     */
    private function verifierAcces(): void
    {
        if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: /connexion');
            exit;
        }
    }

    /**
     * Tableau de bord principal : statistiques + apercu des medecins en attente
     */
    public function tableauBord(): void
    {
        $statistiques = $this->administrateurModel->getStatistiques();
        $medecinsEnAttente = $this->medecinModel->getMedecinsEnAttente();

        require __DIR__ . '/../views/admin/tableau_bord.php';
    }

    /**
     * Liste complete des medecins en attente de validation
     */
    public function listeMedecinsEnAttente(): void
    {
        $medecinsEnAttente = $this->medecinModel->getMedecinsEnAttente();

        require __DIR__ . '/../views/admin/liste_medecins.php';
    }

    /**
     * Valide le compte d'un medecin (appelee en POST)
     */
    public function validerMedecin(): void
    {
        $idMedecin = (int) ($_POST['id_medecin'] ?? 0);

        if ($idMedecin > 0) {
            $this->administrateurModel->validerMedecin($idMedecin);
        }

        header('Location: /admin/medecins-en-attente');
        exit;
    }

    /**
     * Rejette le compte d'un medecin (appelee en POST)
     */
    public function rejeterMedecin(): void
    {
        $idMedecin = (int) ($_POST['id_medecin'] ?? 0);

        if ($idMedecin > 0) {
            $this->administrateurModel->rejeterMedecin($idMedecin);
        }

        header('Location: /admin/medecins-en-attente');
        exit;
    }

    /**
     * Liste tous les patients de la plateforme
     */
    public function listePatients(): void
    {
        $patients = $this->patientModel->getAll();

        require __DIR__ . '/../views/admin/liste_patients.php';
    }

    /**
     * Liste tous les utilisateurs de la plateforme
     */
    public function listeUtilisateurs(): void
    {
        $utilisateurs = $this->administrateurModel->getTousUtilisateurs();

        require __DIR__ . '/../views/admin/liste_utilisateurs.php';
    }

    /**
     * Supprime un utilisateur (appelee en POST)
     */
    public function supprimerUtilisateur(): void
    {
        $idUtilisateur = (int) ($_POST['id_utilisateur'] ?? 0);

        // Empeche l'admin de se supprimer lui-meme par erreur
        if ($idUtilisateur > 0 && $idUtilisateur !== (int) $_SESSION['id_utilisateur']) {
            $this->administrateurModel->supprimerUtilisateur($idUtilisateur);
        }

        header('Location: /admin/utilisateurs');
        exit;
    }
}
