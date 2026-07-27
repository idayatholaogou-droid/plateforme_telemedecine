<?php

/**
 * index.php - Front Controller
 * -----------------------------
 * Point d'entree unique de l'application. Toutes les requetes passent
 * par ici (grace au .htaccess) et sont redirigees vers le bon
 * controleur/methode selon l'URL demandee.
 */

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AdministrateurController.php';
require_once __DIR__ . '/../controllers/PatientController.php';
require_once __DIR__ . '/../controllers/MedecinController.php';
require_once __DIR__ . '/../controllers/RendezVousController.php';
require_once __DIR__ . '/../controllers/ConsultationController.php';
require_once __DIR__ . '/../controllers/OrdonnanceController.php';
require_once __DIR__ . '/../controllers/DocumentController.php';
require_once __DIR__ . '/../controllers/MessageController.php';
require_once __DIR__ . '/../controllers/NotificationController.php';
require_once __DIR__ . '/../controllers/SpecialiteController.php';
require_once __DIR__ . '/../controllers/DisponibiliteController.php';

// Recupere l'URL demandee, sans les parametres GET (?...)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$methode = $_SERVER['REQUEST_METHOD'];

// Table de routage : [methode HTTP, URL] => [Controleur, action]
$routes = [
    'GET'  => [
        '/'                            => ['AuthController', 'afficherConnexion'],
        '/connexion'                   => ['AuthController', 'afficherConnexion'],
        '/inscription/patient'         => ['AuthController', 'afficherInscriptionPatient'],
        '/inscription/medecin'         => ['AuthController', 'afficherInscriptionMedecin'],
        '/deconnexion'                 => ['AuthController', 'seDeconnecter'],
        '/admin/tableau-bord'          => ['AdministrateurController', 'tableauBord'],
        '/admin/medecins-en-attente'   => ['AdministrateurController', 'listeMedecinsEnAttente'],
        '/admin/utilisateurs'          => ['AdministrateurController', 'listeUtilisateurs'],
        '/admin/patients'              => ['AdministrateurController', 'listePatients'],
        '/admin/statistiques'          => ['AdministrateurController', 'statistiques'],
        '/patient/tableau-bord'        => ['PatientController', 'tableauBord'],
        '/patient/profil'              => ['PatientController', 'afficherProfil'],
        '/patient/rechercher-medecin'  => ['PatientController', 'rechercherMedecin'],
        '/medecin/tableau-bord'        => ['MedecinController', 'tableauBord'],
        '/medecin/profil'              => ['MedecinController', 'afficherProfil'],
        '/medecin/dossier-patient'     => ['MedecinController', 'consulterDossierPatient'],
        '/rendezvous/prendre'          => ['RendezVousController', 'afficherPrendre'],
        '/rendezvous/liste'            => ['RendezVousController', 'liste'],
        '/rendezvous/detail'           => ['RendezVousController', 'detail'],
        '/consultation/creer'          => ['ConsultationController', 'afficherCreer'],
        '/consultation/detail'         => ['ConsultationController', 'detail'],
        '/patient/historique'          => ['ConsultationController', 'historiquePatient'],
        '/ordonnance/creer'            => ['OrdonnanceController', 'afficherCreer'],
        '/ordonnance/detail'           => ['OrdonnanceController', 'detail'],
        '/patient/documents'           => ['DocumentController', 'afficherListe'],
        '/document/telecharger'        => ['DocumentController', 'telecharger'],
        '/message/conversation'        => ['MessageController', 'afficherConversation'],
        '/message/liste'               => ['MessageController', 'listeConversations'],
        '/notification/liste'          => ['NotificationController', 'afficherListe'],
        '/admin/specialites'           => ['SpecialiteController', 'afficherListe'],
        '/medecin/disponibilites'      => ['DisponibiliteController', 'afficherListe'],
    ],
    'POST' => [
        '/connexion'                    => ['AuthController', 'seConnecter'],
        '/inscription/patient'          => ['AuthController', 'inscrirePatient'],
        '/inscription/medecin'          => ['AuthController', 'inscrireMedecin'],
        '/admin/valider-medecin'        => ['AdministrateurController', 'validerMedecin'],
        '/admin/rejeter-medecin'        => ['AdministrateurController', 'rejeterMedecin'],
        '/admin/supprimer-utilisateur'  => ['AdministrateurController', 'supprimerUtilisateur'],
        '/patient/modifier-profil'      => ['PatientController', 'modifierProfil'],
        '/medecin/modifier-profil'      => ['MedecinController', 'modifierProfil'],
        '/rendezvous/prendre'           => ['RendezVousController', 'prendre'],
        '/rendezvous/annuler'           => ['RendezVousController', 'annuler'],
        '/rendezvous/confirmer'         => ['RendezVousController', 'confirmer'],
        '/consultation/creer'           => ['ConsultationController', 'creer'],
        '/ordonnance/creer'             => ['OrdonnanceController', 'creer'],
        '/patient/documents/upload'     => ['DocumentController', 'uploader'],
        '/document/supprimer'           => ['DocumentController', 'supprimer'],
        '/message/envoyer'              => ['MessageController', 'envoyer'],
        '/notification/marquer-lue'     => ['NotificationController', 'marquerLue'],
        '/admin/specialites/ajouter'    => ['SpecialiteController', 'ajouter'],
        '/admin/specialites/modifier'   => ['SpecialiteController', 'modifier'],
        '/admin/specialites/supprimer'  => ['SpecialiteController', 'supprimer'],
        '/medecin/disponibilites/ajouter'   => ['DisponibiliteController', 'ajouter'],
        '/medecin/disponibilites/supprimer' => ['DisponibiliteController', 'supprimer'],
    ],
];

// Recherche de la route correspondante
if (isset($routes[$methode][$uri])) {
    [$nomControleur, $action] = $routes[$methode][$uri];

    $controleur = new $nomControleur();
    $controleur->$action();
} else {
    // Aucune route trouvee : page 404
    http_response_code(404);
    require __DIR__ . '/../views/errors/404.php';
}
