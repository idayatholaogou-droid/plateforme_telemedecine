<?php

require_once __DIR__ . '/../models/RendezVousModel.php';
require_once __DIR__ . '/../models/DisponibiliteModel.php';
require_once __DIR__ . '/../models/MedecinModel.php';



class RendezVousController
{
    private RendezVousModel $rendezVousModel;
    private DisponibiliteModel $disponibiliteModel;
    private MedecinModel $medecinModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->rendezVousModel = new RendezVousModel();
        $this->disponibiliteModel = new DisponibiliteModel();
        $this->medecinModel = new MedecinModel();

        $this->verifierConnecte();
    }

    private function verifierConnecte(): void
    {
        if (empty($_SESSION['id_utilisateur'])) {
            header('Location: /connexion');
            exit;
        }
    }

    public function afficherPrendre(): void
    {
        if ($_SESSION['role'] !== 'patient') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $idMedecin = (int) ($_GET['medecin'] ?? 0);
        $medecin = $this->medecinModel->trouverParId($idMedecin);

        if (!$medecin) {
            http_response_code(404);
            echo "Medecin introuvable.";
            return;
        }

        $creneaux = $this->disponibiliteModel->getCreneauxLibres($idMedecin);

        require __DIR__ . '/../views/rendezVous/prendre.php';
    }

    public function prendre(): void
    {
        if ($_SESSION['role'] !== 'patient') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $idPatient = $_SESSION['id_utilisateur'];
        $idMedecin = (int) ($_POST['id_medecin'] ?? 0);
        $idDispo = (int) ($_POST['id_dispo'] ?? 0);
        $motif = trim($_POST['motif'] ?? '');

        $idRdv = $this->rendezVousModel->creer($idPatient, $idMedecin, $idDispo, $motif);

        if (!$idRdv) {
            header('Location: /rendezvous/prendre?medecin=' . $idMedecin . '&erreur=creneau_indisponible');
            exit;
        }

        header('Location: /rendezvous/liste');
        exit;
    }

    public function liste(): void
    {
        $idUtilisateur = $_SESSION['id_utilisateur'];

        if ($_SESSION['role'] === 'patient') {
            $rendezVous = $this->rendezVousModel->getParPatient($idUtilisateur);
        } elseif ($_SESSION['role'] === 'medecin') {
            $rendezVous = $this->rendezVousModel->getParMedecin($idUtilisateur);
        } else {
            $rendezVous = [];
        }

        require __DIR__ . '/../views/rendezVous/liste.php';
    }

    public function annuler(): void
    {
        $idRdv = (int) ($_POST['id_rdv'] ?? 0);

        if ($idRdv > 0) {
            $this->rendezVousModel->annuler($idRdv);
        }

        header('Location: /rendezvous/liste');
        exit;
    }

    public function detail(): void
    {
        $idRdv = (int) ($_GET['id'] ?? 0);
        $rdv = $this->rendezVousModel->trouverDetailComplet($idRdv);

        if (!$rdv) {
            http_response_code(404);
            echo "Rendez-vous introuvable.";
            return;
        }

        $idUtilisateur = $_SESSION['id_utilisateur'];
        $estLePatient = (int) $rdv['id_patient'] === $idUtilisateur;
        $estLeMedecin = (int) $rdv['id_medecin'] === $idUtilisateur;

        if (!$estLePatient && !$estLeMedecin) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        require __DIR__ . '/../views/rendezVous/detail.php';
    }

    public function confirmer(): void
    {
        if ($_SESSION['role'] !== 'medecin') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $idRdv = (int) ($_POST['id_rdv'] ?? 0);
        $idMedecin = $_SESSION['id_utilisateur'];

        if ($idRdv > 0) {
            $this->rendezVousModel->confirmer($idRdv, $idMedecin);
        }

        header('Location: /rendezvous/liste');
        exit;
    }
}
