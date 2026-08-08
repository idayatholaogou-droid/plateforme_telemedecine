<?php

require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../models/MedecinModel.php';
require_once __DIR__ . '/../models/SpecialiteModel.php';
require_once __DIR__ . '/../models/UtilisateurModel.php';

class AuthController
{
    private UtilisateurModel $utilisateurModel;
    private PatientModel $patientModel;
    private MedecinModel $medecinModel;
    private SpecialiteModel $specialiteModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->utilisateurModel = new UtilisateurModel();
        $this->patientModel = new PatientModel();
        $this->medecinModel = new MedecinModel();
        $this->specialiteModel = new SpecialiteModel();
    }

    public function afficherConnexion(): void
    {
        require __DIR__ . '/../views/auth/connexion.php';
    }

    public function seConnecter(): void
    {
        $email = trim($_POST['email'] ?? '');
        $motDePasse = $_POST['mot_de_passe'] ?? '';

        if ($email === '' || $motDePasse === '') {
            $erreur = "Veuillez remplir tous les champs.";
            require __DIR__ . '/../views/auth/connexion.php';
            return;
        }

        $utilisateur = $this->utilisateurModel->seConnecter($email, $motDePasse);

        if (!$utilisateur) {
            $erreur = "Email ou mot de passe incorrect.";
            require __DIR__ . '/../views/auth/connexion.php';
            return;
        }

        $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
        $_SESSION['nom']            = $utilisateur['nom'];
        $_SESSION['prenom']         = $utilisateur['prenom'];
        $_SESSION['role']           = $utilisateur['role'];

        switch ($utilisateur['role']) {
            case 'patient':
                header('Location: /patient/tableau-bord');
                break;
            case 'medecin':
                header('Location: /medecin/tableau-bord');
                break;
            case 'admin':
                header('Location: /admin/tableau-bord');
                break;
            default:
                header('Location: /');
        }
        exit;
    }

    public function afficherInscriptionPatient(): void
    {
        require __DIR__ . '/../views/auth/inscription_patient.php';
    }

   
    public function inscrirePatient(): void
    {
        $données = [
            'nom'                  => trim($_POST['nom'] ?? ''),
            'prenom'               => trim($_POST['prenom'] ?? ''),
            'email'                => trim($_POST['email'] ?? ''),
            'mot_de_passe'         => $_POST['mot_de_passe'] ?? '',
            'telephone'            => trim($_POST['telephone'] ?? ''),
            'date_naissance'       => $_POST['date_naissance'] ?? null,
            'sexe'                 => $_POST['sexe'] ?? null,
            'adresse'              => trim($_POST['adresse'] ?? ''),
            'groupe_sanguin'       => $_POST['groupe_sanguin'] ?? null,
            'antecedents_medicaux' => trim($_POST['antecedents_medicaux'] ?? ''),
        ];

        if ($données['nom'] === '' || $données['prenom'] === '' ||
            $données['email'] === '' || $données['mot_de_passe'] === '') {
            $erreur = "Veuillez remplir tous les champs obligatoires.";
            require __DIR__ . '/../views/auth/inscription_patient.php';
            return;
        }

        if ($this->utilisateurModel->emailExiste($données['email'])) {
            $erreur = "Cet email est deja utilise.";
            require __DIR__ . '/../views/auth/inscription_patient.php';
            return;
        }

        $idUtilisateur = $this->patientModel->inscrire($données);

        $_SESSION['id_utilisateur'] = $idUtilisateur;
        $_SESSION['nom']            = $données['nom'];
        $_SESSION['prenom']         = $données['prenom'];
        $_SESSION['role']           = 'patient';

        header('Location: /patient/tableau-bord');
        exit;
    }

    public function afficherInscriptionMedecin(): void
    {
        $specialites = $this->specialiteModel->getToutes();

        require __DIR__ . '/../views/auth/inscriptionMedecin.php';
    }

    public function inscrireMedecin(): void
    {
        $données = [
            'nom'            => trim($_POST['nom'] ?? ''),
            'prenom'         => trim($_POST['prenom'] ?? ''),
            'email'          => trim($_POST['email'] ?? ''),
            'mot_de_passe'   => $_POST['mot_de_passe'] ?? '',
            'telephone'      => trim($_POST['telephone'] ?? ''),
            'numero_licence' => trim($_POST['numero_licence'] ?? ''),
            'biographie'     => trim($_POST['biographie'] ?? ''),
            'specialites'    => array_map('intval', $_POST['specialites'] ?? []),
        ];

        $specialites = $this->specialiteModel->getToutes();

        if ($données['nom'] === '' || $données['prenom'] === '' ||
            $données['email'] === '' || $données['mot_de_passe'] === '' ||
            $données['numero_licence'] === '') {
            $erreur = "Veuillez remplir tous les champs obligatoires.";
            require __DIR__ . '/../views/auth/inscriptionMedecin.php';
            return;
        }

        if ($this->utilisateurModel->emailExiste($données['email'])) {
            $erreur = "Cet email est deja utilise.";
            require __DIR__ . '/../views/auth/inscriptionMedecin.php';
            return;
        }

        $this->medecinModel->inscrire($données);
        $messageSucces = "Votre inscription a bien été enregistrée. Votre compte doit être validé par un administrateur avant que vous puissiez recevoir des rendez-vous.";
        require __DIR__ . '/../views/auth/connexion.php';
    }

    
    public function seDeconnecter(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: /connexion');
        exit;
    }
}
