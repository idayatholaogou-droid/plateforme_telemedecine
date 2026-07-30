<?php

require_once __DIR__ . '/../models/ConsultationModel.php';
require_once __DIR__ . '/../models/RendezVousModel.php';

/**
 * ConsultationController
 * ------------------------
 * Gere la creation d'une consultation par le medecin (a partir d'un
 * rendez-vous confirme) et sa consultation par le patient ou le medecin.
 */

class ConsultationController
{
    private ConsultationModel $consultationModel;
    private RendezVousModel $rendezVousModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->consultationModel = new ConsultationModel();
        $this->rendezVousModel = new RendezVousModel();

        if (empty($_SESSION['id_utilisateur'])) {
            header('Location: /connexion');
            exit;
        }
    }

    /**
     * Affiche le formulaire de creation de consultation (medecin uniquement)
     */
    public function afficherCreer(): void
    {
        if ($_SESSION['role'] !== 'medecin') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $idRdv = (int) ($_GET['rdv'] ?? 0);
        $rdv = $this->rendezVousModel->trouverParId($idRdv);

        if (!$rdv || (int) $rdv['id_medecin'] !== $_SESSION['id_utilisateur']) {
            http_response_code(404);
            echo "Rendez-vous introuvable.";
            return;
        }

        if ($rdv['statut'] !== 'confirme') {
            echo "Ce rendez-vous doit d'abord etre confirme avant de creer une consultation.";
            return;
        }

        require __DIR__ . '/../views/consultation/creer.php';
    }

    /**
     * Traite la creation de la consultation, puis marque le RDV comme termine
     */
    public function creer(): void
    {
        if ($_SESSION['role'] !== 'medecin') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $idRdv = (int) ($_POST['id_rdv'] ?? 0);
        $rdv = $this->rendezVousModel->trouverParId($idRdv);

        if (!$rdv || (int) $rdv['id_medecin'] !== $_SESSION['id_utilisateur']) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $symptomes = trim($_POST['symptomes'] ?? '');
        $diagnostic = trim($_POST['diagnostic'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        $idConsultation = $this->consultationModel->creer($idRdv, $symptomes, $diagnostic, $notes);

        if ($idConsultation) {
            $this->rendezVousModel->terminer($idRdv);
        }

        header('Location: /consultation/detail?id=' . $idConsultation);
        exit;
    }

    /**
     * Affiche le detail d'une consultation
     * (accessible au patient concerne et au medecin concerne uniquement)
     */
    public function detail(): void
    {
        $idConsultation = (int) ($_GET['id'] ?? 0);
        $consultation = $this->consultationModel->trouverParId($idConsultation);

        if (!$consultation) {
            http_response_code(404);
            echo "Consultation introuvable.";
            return;
        }

        $idUtilisateur = $_SESSION['id_utilisateur'];
        $estLePatient = $_SESSION['role'] === 'patient' && (int) $consultation['id_patient'] === $idUtilisateur;
        $estLeMedecin = $_SESSION['role'] === 'medecin' && (int) $consultation['id_medecin'] === $idUtilisateur;

        if (!$estLePatient && !$estLeMedecin) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        require __DIR__ . '/../views/consultation/detail.php';
    }

    /**
     * Historique des consultations du patient connecte
     */
    public function historiquePatient(): void
    {
        if ($_SESSION['role'] !== 'patient') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $historique = $this->consultationModel->getHistoriqueParPatient($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/patient/historique.php';
    }
}
