<?php

require_once __DIR__ . '/../models/SpecialiteModel.php';

/**
 * SpecialiteController
 * -----------------------
 * CRUD des specialites medicales, reserve a l'administrateur.
 */

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

    /**
     * Seul l'administrateur peut gerer les specialites
     */
    private function verifierAcces(): void
    {
        if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: /connexion');
            exit;
        }
    }

    /**
     * Affiche la liste des specialites (avec formulaire d'ajout)
     */
    public function afficherListe(): void
    {
        $specialites = $this->specialiteModel->getToutes();

        require __DIR__ . '/../views/admin/specialites.php';
    }

    /**
     * Traite l'ajout d'une nouvelle specialite
     */
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

    /**
     * Traite la modification d'une specialite
     */
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

    /**
     * Traite la suppression d'une specialite
     */
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
