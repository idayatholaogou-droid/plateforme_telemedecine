<?php

require_once __DIR__ . '/../models/NotificationModel.php';

/**
 * NotificationController
 * -------------------------
 * Affiche et gere les notifications de l'utilisateur connecte
 * (patient, medecin ou admin).
 */

class NotificationController
{
    private NotificationModel $notificationModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->notificationModel = new NotificationModel();

        if (empty($_SESSION['id_utilisateur'])) {
            header('Location: /connexion');
            exit;
        }
    }

    /**
     * Affiche la liste des notifications, et les marque toutes comme lues
     */
    public function afficherListe(): void
    {
        $idUtilisateur = $_SESSION['id_utilisateur'];

        $notifications = $this->notificationModel->getParUtilisateur($idUtilisateur);

        $this->notificationModel->marquerToutesCommeLues($idUtilisateur);

        require __DIR__ . '/../views/notification/liste.php';
    }

    /**
     * Marque une notification precise comme lue (ex: clic individuel, AJAX)
     */
    public function marquerLue(): void
    {
        $idNotification = (int) ($_POST['id_notification'] ?? 0);
        $idUtilisateur = $_SESSION['id_utilisateur'];

        if ($idNotification > 0) {
            $this->notificationModel->marquerCommeLue($idNotification, $idUtilisateur);
        }

        header('Location: /notification/liste');
        exit;
    }
}
