<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class StaffController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        Security::requireRole(['super_admin']); // EXCLUSIF Super Admin
    }

    public function index() {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT s.*, u.email, u.role, u.date_creation 
            FROM staff s 
            JOIN utilisateur u ON s.fk_user = u.id_user 
            ORDER BY u.role, s.nom
        ");
        $staff = $stmt->fetchAll();

        $this->view('staff/index', ['staff' => $staff]);
    }

    public function create() {
        $db = Database::getInstance();
        $classes = $db->query("SELECT * FROM classe ORDER BY nom_classe")->fetchAll();
        $this->view('staff/form', [
            'action' => 'store',
            'title' => 'Ajouter un Membre du Staff',
            'member' => null,
            'classes' => $classes
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $nom = Security::escape($_POST['nom']);
            $prenom = Security::escape($_POST['prenom']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $role = $_POST['role'] === 'super_admin' ? 'super_admin' : 'responsable';
            $fk_classe = ($role === 'responsable') ? ($_POST['fk_classe'] ?? null) : null;

            // 1. Créer le compte utilisateur
            $hash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO utilisateur (email, password_hash, role) VALUES (?, ?, ?)");
            $stmt->execute([$email, $hash, $role]);
            $user_id = $db->lastInsertId();

            // 2. Créer l'entité staff liée
            $stmt2 = $db->prepare("INSERT INTO staff (nom, prenom, fk_user, fk_classe) VALUES (?, ?, ?, ?)");
            $stmt2->execute([$nom, $prenom, $user_id, $fk_classe]);

            $this->redirect('staff');
        }
    }

    public function edit($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT s.*, u.email, u.role 
            FROM staff s 
            JOIN utilisateur u ON s.fk_user = u.id_user 
            WHERE s.id_staff = ?
        ");
        $stmt->execute([$id]);
        $member = $stmt->fetch();
        $classes = $db->query("SELECT * FROM classe ORDER BY nom_classe")->fetchAll();

        if ($member) {
            $this->view('staff/form', [
                'action' => 'update/' . $id,
                'title' => 'Modifier le Staff',
                'member' => $member,
                'classes' => $classes
            ]);
        } else {
            $this->redirect('staff');
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $nom = Security::escape($_POST['nom']);
            $prenom = Security::escape($_POST['prenom']);
            $role = $_POST['role'];
            $fk_classe = ($role === 'responsable') ? ($_POST['fk_classe'] ?? null) : null;

            // Get user_id
            $gs = $db->prepare("SELECT fk_user FROM staff WHERE id_staff = ?");
            $gs->execute([$id]);
            $uid = $gs->fetch()->fk_user;

            $stmt = $db->prepare("UPDATE staff SET nom = ?, prenom = ?, fk_classe = ? WHERE id_staff = ?");
            $stmt->execute([$nom, $prenom, $fk_classe, $id]);

            $stmt2 = $db->prepare("UPDATE utilisateur SET role = ? WHERE id_user = ?");
            $stmt2->execute([$role, $uid]);

            $this->redirect('staff');
        }
    }

    public function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT fk_user FROM staff WHERE id_staff = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();

        if($res) {
            // Delete user triggers delete staff thanks to CASCADE
            $stmt2 = $db->prepare("DELETE FROM utilisateur WHERE id_user = ?");
            $stmt2->execute([$res->fk_user]);
        }
        $this->redirect('staff');
    }
}
