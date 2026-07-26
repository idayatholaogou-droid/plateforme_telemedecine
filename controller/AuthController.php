<?php

require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../models/UtilisateurModel.php';

/**
 * AuthController
 * --------------
 * Gere la connexion, l'inscription et la deconnexion.
 * Utilise session PHP pour maintenir l'utilisateur connecte.
 */

class AuthController
{
    private UtilisateurModel $utilisateurModel;
    private PatientModel $patientModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->utilisateurModel = new UtilisateurModel();
        $this->patientModel = new PatientModel();
    }

    /**
     * Affiche le formulaire de connexion
     */
    public function afficherConnexion(): void
    {
        require __DIR__ . '/../views/auth/connexion.php';
    }

    /**
     * Traite la soumission du formulaire de connexion
     */
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

        // Stocke les infos essentielles en session
        $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
        $_SESSION['nom']            = $utilisateur['nom'];
        $_SESSION['prenom']         = $utilisateur['prenom'];
        $_SESSION['role']           = $utilisateur['role'];

        // Redirection selon le role
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

    /**
     * Affiche le formulaire d'inscription patient
     */
    public function afficherInscriptionPatient(): void
    {
        require __DIR__ . '/../views/auth/inscription_patient.php';
    }

    /**
     * Traite l'inscription d'un patient
     */
    public function inscrirePatient(): void
    {
        $donnees = [
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

        // Validation minimale
        if ($donnees['nom'] === '' || $donnees['prenom'] === '' ||
            $donnees['email'] === '' || $donnees['mot_de_passe'] === '') {
            $erreur = "Veuillez remplir tous les champs obligatoires.";
            require __DIR__ . '/../views/auth/inscription_patient.php';
            return;
        }

        if ($this->utilisateurModel->emailExiste($donnees['email'])) {
            $erreur = "Cet email est deja utilise.";
            require __DIR__ . '/../views/auth/inscription_patient.php';
            return;
        }

        $idUtilisateur = $this->patientModel->inscrire($donnees);

        // Connexion automatique apres inscription
        $_SESSION['id_utilisateur'] = $idUtilisateur;
        $_SESSION['nom']            = $donnees['nom'];
        $_SESSION['prenom']         = $donnees['prenom'];
        $_SESSION['role']           = 'patient';

        header('Location: /patient/tableau-bord');
        exit;
    }

    /**
     * Deconnecte l'utilisateur (detruit la session)
     */
    public function seDeconnecter(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: /connexion');
        exit;
    }
}
