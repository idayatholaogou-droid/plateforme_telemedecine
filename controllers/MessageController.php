<?php

require_once __DIR__ . '/../models/ConversationModel.php';
require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../models/RendezVousModel.php';


class MessageController
{
    private ConversationModel $conversationModel;
    private MessageModel $messageModel;
    private RendezVousModel $rendezVousModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->conversationModel = new ConversationModel();
        $this->messageModel = new MessageModel();
        $this->rendezVousModel = new RendezVousModel();

        if (empty($_SESSION['id_utilisateur'])) {
            header('Location: /connexion');
            exit;
        }
    }

    
    private function verifierParticipant(array $conversation): bool
    {
        $idUtilisateur = $_SESSION['id_utilisateur'];
        return (int) $conversation['id_patient'] === $idUtilisateur
            || (int) $conversation['id_medecin'] === $idUtilisateur;
    }

    public function afficherConversation(): void
    {
        $idRdv = (int) ($_GET['rdv'] ?? 0);
        $rdv = $this->rendezVousModel->trouverParId($idRdv);

        if (!$rdv) {
            http_response_code(404);
            echo "Rendez-vous introuvable.";
            return;
        }

        $idUtilisateur = $_SESSION['id_utilisateur'];
        if ((int) $rdv['id_patient'] !== $idUtilisateur && (int) $rdv['id_medecin'] !== $idUtilisateur) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        $idConversation = $this->conversationModel->creerPourRdv($idRdv);
        $conversation = $this->conversationModel->trouverParId($idConversation);
        $messages = $this->messageModel->getParConversation($idConversation);

        $this->messageModel->marquerCommeLus($idConversation, $idUtilisateur);

        require __DIR__ . '/../views/message/conversation.php';
    }

    
    public function envoyer(): void
    {
        $idConversation = (int) ($_POST['id_conversation'] ?? 0);
        $contenu = trim($_POST['contenu'] ?? '');

        $conversation = $this->conversationModel->trouverParId($idConversation);

        if (!$conversation || !$this->verifierParticipant($conversation)) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            return;
        }

        if ($contenu !== '') {
            $this->messageModel->envoyer($idConversation, $_SESSION['id_utilisateur'], $contenu);
        }

        header('Location: /message/conversation?rdv=' . $conversation['id_rdv']);
        exit;
    }

    
    public function listeConversations(): void
    {
        $conversations = $this->conversationModel->getParUtilisateur(
            $_SESSION['id_utilisateur'],
            $_SESSION['role']
        );

        require __DIR__ . '/../views/message/liste.php';
    }
}
