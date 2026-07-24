<?php

require_once __DIR__ . '/../models/OrdonnanceModel.php';
require_once __DIR__ . '/../models/ConsultationModel.php';

/**
 * OrdonnanceController
 * ----------------------
 * Gere la redaction d'ordonnances par le medecin, a partir d'une
 * consultation existante, et leur consultation par le patient concerne.
 */

class OrdonnanceController
{
    private OrdonnanceModel $ordonnanceModel;
    private ConsultationModel $consultationModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->ordonnanceModel = new OrdonnanceModel();
        $this->consultationModel = new ConsultationModel();

        if (empty($_SESSION['id_utilisateur'])) {
            header('Location: /connexion');
            exit;
        }
    }

    /**
     * Affiche le formulaire de redaction d'ordonnance (medecin uniquement)
     */
    public function afficherCreer(): void
    {
        if ($_SESSION['role'] !== 'medecin') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $idConsultation = (int) ($_GET['consultation'] ?? 0);
        $consultation = $this->consultationModel->trouverParId($idConsultation);

        if (!$consultation || (int) $consultation['id_medecin'] !== $_SESSION['id_utilisateur']) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        require __DIR__ . '/../views/ordonnance/creer.php';
    }

    /**
     * Traite la creation de l'ordonnance avec ses lignes de medicaments
     */
    public function creer(): void
    {
        if ($_SESSION['role'] !== 'medecin') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $idConsultation = (int) ($_POST['id_consultation'] ?? 0);
        $consultation = $this->consultationModel->trouverParId($idConsultation);

        if (!$consultation || (int) $consultation['id_medecin'] !== $_SESSION['id_utilisateur']) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        // Les champs du formulaire arrivent sous forme de tableaux paralleles
        // nom_medicament[], posologie[], duree[], quantite[]
        $noms = $_POST['nom_medicament'] ?? [];
        $posologies = $_POST['posologie'] ?? [];
        $durees = $_POST['duree'] ?? [];
        $quantites = $_POST['quantite'] ?? [];

        $medicaments = [];
        foreach ($noms as $i => $nom) {
            $nom = trim($nom);
            if ($nom === '') {
                continue; // ignore les lignes vides
            }
            $medicaments[] = [
                'nom'       => $nom,
                'posologie' => trim($posologies[$i] ?? ''),
                'duree'     => $durees[$i] !== '' ? (int) $durees[$i] : null,
                'quantite'  => $quantites[$i] !== '' ? (int) $quantites[$i] : 1,
            ];
        }

        if (empty($medicaments)) {
            $erreur = "Veuillez ajouter au moins un medicament.";
            require __DIR__ . '/../views/ordonnance/creer.php';
            return;
        }

        $idOrdonnance = $this->ordonnanceModel->creer($idConsultation, $medicaments);

        header('Location: /ordonnance/detail?id=' . $idOrdonnance);
        exit;
    }

    /**
     * Affiche le detail d'une ordonnance (patient ou medecin concerne uniquement)
     */
    public function detail(): void
    {
        $idOrdonnance = (int) ($_GET['id'] ?? 0);
        $ordonnance = $this->ordonnanceModel->trouverParId($idOrdonnance);

        if (!$ordonnance) {
            http_response_code(404);
            echo "Ordonnance introuvable.";
            return;
        }

        $idUtilisateur = $_SESSION['id_utilisateur'];
        $estLePatient = $_SESSION['role'] === 'patient' && (int) $ordonnance['id_patient'] === $idUtilisateur;
        $estLeMedecin = $_SESSION['role'] === 'medecin' && (int) $ordonnance['id_medecin'] === $idUtilisateur;

        if (!$estLePatient && !$estLeMedecin) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        require __DIR__ . '/../views/ordonnance/detail.php';
    }
}
