<?php

require_once __DIR__ . '/../models/SpecialiteModel.php';

class SpecialiteController
{
    private SpecialiteModel $specialiteModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->specialiteModel = new SpecialiteModel();

        $this->verifierAcces();
    }

    private function verifierAcces(): void
    {
        if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: /connexion');
            exit;
        }
    }

    public function afficherListe(): void
    {
        $specialites = $this->specialiteModel->getToutes();

        require __DIR__ . '/../views/admin/specialites.php';
    }

    public function ajouter(): void
    {
        $libelle = trim($_POST['libelle'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($libelle !== '') {
            $this->specialiteModel->ajouter($libelle, $description);
        }

        header('Location: /admin/specialites');
        exit;
    }

    public function modifier(): void
    {
        $idSpecialite = (int) ($_POST['id_specialite'] ?? 0);
        $libelle = trim($_POST['libelle'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($idSpecialite > 0 && $libelle !== '') {
            $this->specialiteModel->modifier($idSpecialite, $libelle, $description);
        }

        header('Location: /admin/specialites');
        exit;
    }

    public function supprimer(): void
    {
        $idSpecialite = (int) ($_POST['id_specialite'] ?? 0);

        if ($idSpecialite > 0) {
            $this->specialiteModel->supprimer($idSpecialite);
        }

        header('Location: /admin/specialites');
        exit;
    }
}
