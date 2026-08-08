<?php

require_once __DIR__ . '/../models/DocumentModel.php';
require_once __DIR__ . '/../models/RendezVousModel.php';


class DocumentController
{
    private DocumentModel $documentModel;
    private RendezVousModel $rendezVousModel;

    private const DOSSIER_STOCKAGE = __DIR__ . '/../storage/documents/';
    private const TYPES_AUTORISES = ['application/pdf', 'image/jpeg', 'image/png'];
    private const TAILLE_MAX = 5 * 1024 * 1024; // 5 Mo

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->documentModel = new DocumentModel();
        $this->rendezVousModel = new RendezVousModel();

        if (empty($_SESSION['id_utilisateur'])) {
            header('Location: /connexion');
            exit;
        }

        if (!is_dir(self::DOSSIER_STOCKAGE)) {
            mkdir(self::DOSSIER_STOCKAGE, 0755, true);
        }
    }

   
    public function afficherListe(): void
    {
        if ($_SESSION['role'] !== 'patient') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $documents = $this->documentModel->getParPatient($_SESSION['id_utilisateur']);

        require __DIR__ . '/../views/patient/documents.php';
    }

   
    public function uploader(): void
    {
        if ($_SESSION['role'] !== 'patient') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $idPatient = $_SESSION['id_utilisateur'];
        $type = trim($_POST['type'] ?? 'autre');

        if (empty($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
            $erreur = "Veuillez selectionner un fichier valide.";
            $documents = $this->documentModel->getParPatient($idPatient);
            require __DIR__ . '/../views/patient/documents.php';
            return;
        }

        $fichier = $_FILES['fichier'];

        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $typeMime = finfo_file($finfo, $fichier['tmp_name']);
        finfo_close($finfo);

        if (!in_array($typeMime, self::TYPES_AUTORISES, true)) {
            $erreur = "Type de fichier non autorise. Seuls les PDF, JPEG et PNG sont acceptes.";
            $documents = $this->documentModel->getParPatient($idPatient);
            require __DIR__ . '/../views/patient/documents.php';
            return;
        }

        if ($fichier['size'] > self::TAILLE_MAX) {
            $erreur = "Le fichier depasse la taille maximale autorisee (5 Mo).";
            $documents = $this->documentModel->getParPatient($idPatient);
            require __DIR__ . '/../views/patient/documents.php';
            return;
        }

        
        $extension = pathinfo($fichier['name'], PATHINFO_EXTENSION);
        $nomFichier = uniqid('doc_', true) . '.' . $extension;
        $cheminComplet = self::DOSSIER_STOCKAGE . $nomFichier;

        if (!move_uploaded_file($fichier['tmp_name'], $cheminComplet)) {
            $erreur = "Erreur lors de l'enregistrement du fichier.";
            $documents = $this->documentModel->getParPatient($idPatient);
            require __DIR__ . '/../views/patient/documents.php';
            return;
        }

        
        $this->documentModel->ajouter($idPatient, $type, $nomFichier);

        header('Location: /patient/documents');
        exit;
    }

   
    public function telecharger(): void
    {
        $idDocument = (int) ($_GET['id'] ?? 0);
        $document = $this->documentModel->trouverParId($idDocument);

        if (!$document) {
            http_response_code(404);
            echo "Document introuvable.";
            return;
        }

        $idUtilisateur = $_SESSION['id_utilisateur'];
        $estProprietaire = $_SESSION['role'] === 'patient' && (int) $document['id_patient'] === $idUtilisateur;
        $estMedecinAutorise = $_SESSION['role'] === 'medecin'
            && $this->rendezVousModel->medecinAAccesPatient($idUtilisateur, (int) $document['id_patient']);

        if (!$estProprietaire && !$estMedecinAutorise) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $cheminComplet = self::DOSSIER_STOCKAGE . $document['chemin_fichier'];

        if (!file_exists($cheminComplet)) {
            http_response_code(404);
            echo "Fichier introuvable sur le serveur.";
            return;
        }

        // Envoi du fichier au navigateur
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($document['chemin_fichier']) . '"');
        header('Content-Length: ' . filesize($cheminComplet));
        readfile($cheminComplet);
        exit;
    }

   
    public function supprimer(): void
    {
        $idDocument = (int) ($_POST['id_document'] ?? 0);
        $idPatient = $_SESSION['id_utilisateur'];

        $document = $this->documentModel->trouverParId($idDocument);

        if ($document && (int) $document['id_patient'] === $idPatient) {
            $cheminComplet = self::DOSSIER_STOCKAGE . $document['chemin_fichier'];
            if (file_exists($cheminComplet)) {
                unlink($cheminComplet);
            }
            $this->documentModel->supprimer($idDocument, $idPatient);
        }

        header('Location: /patient/documents');
        exit;
    }
}
