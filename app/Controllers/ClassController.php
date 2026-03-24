<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class ClassController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        Security::requireRole('super_admin');
    }

    public function index() {
        $db = Database::getInstance();
        $classes = $db->query("SELECT * FROM classe ORDER BY nom_classe")->fetchAll();
        $this->view('class/index', ['classes' => $classes]);
    }

    public function create() {
        $this->view('class/form', [
            'action' => 'store',
            'title' => 'Ajouter une Classe',
            'class_data' => null
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $nom = Security::escape($_POST['nom_classe']);
            $niveau = Security::escape($_POST['niveau']);
            
            $stmtY = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1");
            $yearId = $stmtY->fetch()->id_annee ?? 1;

            $stmt = $db->prepare("INSERT INTO classe (nom_classe, niveau, fk_annee) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $niveau, $yearId]);
            $this->redirect('class');
        }
    }

    public function edit($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM classe WHERE id_classe = ?");
        $stmt->execute([$id]);
        $c = $stmt->fetch();

        if ($c) {
            $this->view('class/form', [
                'action' => 'update/' . $id,
                'title' => 'Modifier la Classe',
                'class_data' => $c
            ]);
        } else {
            $this->redirect('class');
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $nom = Security::escape($_POST['nom_classe']);
            $niveau = Security::escape($_POST['niveau']);

            $stmt = $db->prepare("UPDATE classe SET nom_classe = ?, niveau = ? WHERE id_classe = ?");
            $stmt->execute([$nom, $niveau, $id]);
            $this->redirect('class');
        }
    }

    public function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM classe WHERE id_classe = ?");
        $stmt->execute([$id]);
        $this->redirect('class');
    }
}
