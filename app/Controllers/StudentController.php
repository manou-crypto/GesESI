<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class StudentController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        Security::requireRole(['super_admin', 'responsable']); // Seuls admins/resp gèrent le CRUD des élèves
    }

    public function index() {
        $db = Database::getInstance();
        $id_classe = ($_SESSION['role'] === 'responsable') ? ($_SESSION['id_classe'] ?? null) : null;
        
        $sql = "
            SELECT e.*, u.email, c.id_classe, c.nom_classe as section, c.niveau
            FROM etudiant e 
            JOIN utilisateur u ON e.fk_user = u.id_user
            LEFT JOIN inscription i ON e.id_etudiant = i.fk_etudiant AND i.fk_annee = (SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1)
            LEFT JOIN classe c ON i.fk_classe = c.id_classe
        ";

        if ($id_classe) {
            $sql .= " WHERE i.fk_classe = ? ";
            $sql .= " ORDER BY e.nom, e.prenom ";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_classe]);
        } else {
            $sql .= " ORDER BY e.nom, e.prenom ";
            $stmt = $db->query($sql);
        }
        
        $students = $stmt->fetchAll();

        $this->view('dashboard/students', [
            'role' => $_SESSION['role'],
            'students' => $students
        ]);
    }

    public function create() {
        $db = Database::getInstance();
        if ($_SESSION['role'] === 'responsable') {
            $id_classe = $_SESSION['id_classe'] ?? null;
            $classes = $db->prepare("SELECT * FROM classe WHERE id_classe = ?");
            $classes->execute([$id_classe]);
            $classes = $classes->fetchAll();
        } else {
            $classes = $db->query("SELECT * FROM classe ORDER BY nom_classe")->fetchAll();
        }

        $this->view('student/form', [
            'action' => 'store',
            'title' => 'Ajouter un étudiant',
            'student' => null,
            'classes' => $classes
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $nom = Security::escape($_POST['nom']);
            $prenom = Security::escape($_POST['prenom']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            // On gère 'telephone' de manière optionnelle car l'utilisateur semble l'avoir retiré du schéma
            $telephone = isset($_POST['telephone']) ? Security::escape($_POST['telephone']) : null;
            $matricule = 'E' . time(); // Génération auto

            // 1. Créer le compte utilisateur générique pour cet étudiant
            $hash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO utilisateur (email, password_hash, role) VALUES (?, ?, 'etudiant')");
            $stmt->execute([$email, $hash]);
            $user_id = $db->lastInsertId();

            // Suppression provisoire de telephone dans l'insert si la colonne a été retirée du schéma
            $stmt2 = $db->prepare("INSERT INTO etudiant (matricule, nom, prenom, fk_user) VALUES (?, ?, ?, ?)");
            $stmt2->execute([$matricule, $nom, $prenom, $user_id]);
            $etudiant_id = $db->lastInsertId();

            // 3. Inscription dans la classe pour l'année active
            $fk_classe = $_POST['classe'] ?? null;
            
            // Sécurité : forcer la classe du responsable
            if ($_SESSION['role'] === 'responsable') {
                $fk_classe = $_SESSION['id_classe'];
            }
            $stmtYear = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1");
            $year = $stmtYear->fetch();
            if ($year && $fk_classe) {
                $stmtInsc = $db->prepare("INSERT INTO inscription (fk_etudiant, fk_classe, fk_annee) VALUES (?, ?, ?)");
                $stmtInsc->execute([$etudiant_id, $fk_classe, $year->id_annee]);
            }

            $this->redirect('dashboard/students');
        }
    }

    public function edit($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT e.*, u.email, i.fk_classe 
            FROM etudiant e 
            JOIN utilisateur u ON e.fk_user = u.id_user 
            LEFT JOIN inscription i ON e.id_etudiant = i.fk_etudiant AND i.fk_annee = (SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1)
            WHERE e.id_etudiant = ?
        ");
        $stmt->execute([$id]);
        $student = $stmt->fetch();

        // Sécurité : Vérifier que l'élève appartient à la classe du responsable
        if ($_SESSION['role'] === 'responsable' && ($student->fk_classe != $_SESSION['id_classe'])) {
            $this->redirect('dashboard/students&error=AccessDenied');
        }

        if ($_SESSION['role'] === 'responsable') {
            $id_classe = $_SESSION['id_classe'] ?? null;
            $classes = $db->prepare("SELECT * FROM classe WHERE id_classe = ?");
            $classes->execute([$id_classe]);
            $classes = $classes->fetchAll();
        } else {
            $classes = $db->query("SELECT * FROM classe ORDER BY nom_classe")->fetchAll();
        }

        if ($student) {
            $this->view('student/form', [
                'action' => 'update/' . $id,
                'title' => 'Modifier l\'étudiant',
                'student' => $student,
                'classes' => $classes
            ]);
        } else {
            $this->redirect('dashboard/students');
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $nom = Security::escape($_POST['nom']);
            $prenom = Security::escape($_POST['prenom']);
            // $telephone = Security::escape($_POST['telephone']); // Removed as per schema update

            $stmt = $db->prepare("UPDATE etudiant SET nom = ?, prenom = ? WHERE id_etudiant = ?");
            $stmt->execute([$nom, $prenom, $id]);

            // Mise à jour de l'inscription
            $fk_classe = $_POST['classe'] ?? null;
            $stmtYear = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1");
            $year = $stmtYear->fetch();
            
            if ($year && $fk_classe) {
                // Vérifier si une inscription existe déjà pour cette année
                $chk = $db->prepare("SELECT id_inscription FROM inscription WHERE fk_etudiant = ? AND fk_annee = ?");
                $chk->execute([$id, $year->id_annee]);
                if($chk->fetch()) {
                    $upd = $db->prepare("UPDATE inscription SET fk_classe = ? WHERE fk_etudiant = ? AND fk_annee = ?");
                    $upd->execute([$fk_classe, $id, $year->id_annee]);
                } else {
                    $ins = $db->prepare("INSERT INTO inscription (fk_etudiant, fk_classe, fk_annee) VALUES (?, ?, ?)");
                    $ins->execute([$id, $fk_classe, $year->id_annee]);
                }
            }

            $this->redirect('dashboard/students');
        }
    }

    public function delete($id) {
        $db = Database::getInstance();
        // CASCADING DELETE va aussi supprimer l'utilisateur associé car FOREIGN KEY ON DELETE CASCADE
        // Mais nous devons supprimer dans utilisateur d'abord pour être propre si ça bloque:
        $stmt = $db->prepare("SELECT fk_user FROM etudiant WHERE id_etudiant = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();

        if($res) {
            $stmt2 = $db->prepare("DELETE FROM utilisateur WHERE id_user = ?");
            $stmt2->execute([$res->fk_user]);
        }
        $this->redirect('dashboard/students');
    }
}
