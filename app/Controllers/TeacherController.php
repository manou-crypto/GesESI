<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class TeacherController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        Security::requireRole(['super_admin', 'responsable']);
    }

    public function index() {
        $db = Database::getInstance();

        // Tous les profs avec email
        $teachers = $db->query("SELECT p.*, u.email FROM professeur p JOIN utilisateur u ON p.fk_user = u.id_user")->fetchAll();

        // Classes ayant des profs affectés (filtré pour responsable)
        $id_classe = ($_SESSION['role'] === 'responsable') ? ($_SESSION['id_classe'] ?? null) : null;
        
        $sqlClasses = "
            SELECT DISTINCT cl.id_classe, cl.nom_classe, cl.niveau,
                   aa.libelle AS annee_libelle
            FROM classe cl
            JOIN affectation_cours ac ON cl.id_classe = ac.fk_classe
            JOIN professeur p ON ac.fk_prof = p.id_prof
            JOIN annee_academique aa ON cl.fk_annee = aa.id_annee
        ";

        if ($id_classe) {
            $sqlClasses .= " WHERE cl.id_classe = ? ";
            $sqlClasses .= " ORDER BY aa.libelle DESC, cl.nom_classe ";
            $stmtCl = $db->prepare($sqlClasses);
            $stmtCl->execute([$id_classe]);
        } else {
            $sqlClasses .= " ORDER BY aa.libelle DESC, cl.nom_classe ";
            $stmtCl = $db->query($sqlClasses);
        }
        $classesprofs = $stmtCl->fetchAll();

        // Pour chaque classe, récupérer ses profs avec leurs cours
        $profsByClass = [];
        foreach ($classesprofs as $cl) {
            $stmt = $db->prepare("
                SELECT DISTINCT p.*, u.email, co.libelle AS cours_libelle, co.ue_nom
                FROM professeur p
                JOIN utilisateur u ON p.fk_user = u.id_user
                JOIN affectation_cours ac ON p.id_prof = ac.fk_prof
                JOIN cours co ON ac.fk_cours = co.id_cours
                WHERE ac.fk_classe = ?
                ORDER BY p.nom
            ");
            $stmt->execute([$cl->id_classe]);
            $profsByClass[$cl->id_classe] = $stmt->fetchAll();
        }

        $this->view('teacher/index', [
            'teachers'    => $teachers,
            'classesprofs' => $classesprofs,
            'profsByClass' => $profsByClass
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
        $courses = $db->query("SELECT * FROM cours ORDER BY libelle")->fetchAll();

        $this->view('teacher/form', [
            'action' => 'store',
            'title' => 'Ajouter un Professeur',
            'teacher' => null,
            'classes' => $classes,
            'courses' => $courses
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $nom = Security::escape($_POST['nom']);
            $prenom = Security::escape($_POST['prenom']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            // $telephone = Security::escape($_POST['telephone']); // Removed as per schema update
            $specialite = Security::escape($_POST['specialite']);
            $matricule = 'P' . time(); 

            // 1. Créer le compte utilisateur
            $hash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO utilisateur (email, password_hash, role) VALUES (?, ?, 'professeur')");
            $stmt->execute([$email, $hash]);
            $user_id = $db->lastInsertId();

            // 2. Créer le professeur lié
            $stmt2 = $db->prepare("INSERT INTO professeur (matricule, nom, prenom, specialite, fk_user) VALUES (?, ?, ?, ?, ?)");
            $stmt2->execute([$matricule, $nom, $prenom, $specialite, $user_id]);
            $prof_id = $db->lastInsertId();

            // 3. Affectation Cours / Classe
            $fk_cours = $_POST['fk_cours'] ?? null;
            $fk_classe = $_POST['fk_classe'] ?? null;
            $stmtYear = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1");
            $year = $stmtYear->fetch();

            if ($year && $fk_cours && $fk_classe) {
                $stmtAff = $db->prepare("INSERT INTO affectation_cours (fk_prof, fk_cours, fk_classe, fk_annee) VALUES (?, ?, ?, ?)");
                $stmtAff->execute([$prof_id, $fk_cours, $fk_classe, $year->id_annee]);
            }

            $this->redirect('teacher');
        }
    }

    public function edit($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, u.email, a.fk_cours, a.fk_classe 
            FROM professeur p 
            JOIN utilisateur u ON p.fk_user = u.id_user 
            LEFT JOIN affectation_cours a ON p.id_prof = a.fk_prof AND a.fk_annee = (SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1)
            WHERE p.id_prof = ?
        ");
        $stmt->execute([$id]);
        $teacher = $stmt->fetch();

        // Sécurité : Vérifier que le prof est affecté à la classe du responsable
        if ($_SESSION['role'] === 'responsable' && ($teacher->fk_classe != $_SESSION['id_classe'])) {
             $this->redirect('teacher&error=AccessDenied');
        }

        if ($_SESSION['role'] === 'responsable') {
            $id_classe = $_SESSION['id_classe'] ?? null;
            $classes = $db->prepare("SELECT * FROM classe WHERE id_classe = ?");
            $classes->execute([$id_classe]);
            $classes = $classes->fetchAll();
        } else {
            $classes = $db->query("SELECT * FROM classe ORDER BY nom_classe")->fetchAll();
        }
        
        $courses = $db->query("SELECT * FROM cours ORDER BY libelle")->fetchAll();

        if ($teacher) {
            $this->view('teacher/form', [
                'action' => 'update/' . $id,
                'title' => 'Modifier le Professeur',
                'teacher' => $teacher,
                'classes' => $classes,
                'courses' => $courses
            ]);
        } else {
            $this->redirect('teacher');
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $nom = Security::escape($_POST['nom']);
            $prenom = Security::escape($_POST['prenom']);
            // $telephone = Security::escape($_POST['telephone']);
            $specialite = Security::escape($_POST['specialite']);

            $stmt = $db->prepare("UPDATE professeur SET nom = ?, prenom = ?, specialite = ? WHERE id_prof = ?");
            $stmt->execute([$nom, $prenom, $specialite, $id]);

            $fk_cours = $_POST['fk_cours'] ?? null;
            $fk_classe = $_POST['fk_classe'] ?? null;
            $stmtYear = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1");
            $year = $stmtYear->fetch();

            if ($year && $fk_cours && $fk_classe) {
                // Check existante
                $chk = $db->prepare("SELECT id_affectation FROM affectation_cours WHERE fk_prof = ? AND fk_annee = ?");
                $chk->execute([$id, $year->id_annee]);
                if($chk->fetch()) {
                    $upd = $db->prepare("UPDATE affectation_cours SET fk_cours = ?, fk_classe = ? WHERE fk_prof = ? AND fk_annee = ?");
                    $upd->execute([$fk_cours, $fk_classe, $id, $year->id_annee]);
                } else {
                    $ins = $db->prepare("INSERT INTO affectation_cours (fk_prof, fk_cours, fk_classe, fk_annee) VALUES (?, ?, ?, ?)");
                    $ins->execute([$id, $fk_cours, $fk_classe, $year->id_annee]);
                }
            }

            $this->redirect('teacher');
        }
    }

    public function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT fk_user FROM professeur WHERE id_prof = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();

        if($res) {
            $stmt2 = $db->prepare("DELETE FROM utilisateur WHERE id_user = ?");
            $stmt2->execute([$res->fk_user]);
        }
        $this->redirect('teacher');
    }
}

