<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class CourseController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        Security::requireRole('super_admin');
    }

    public function index() {
        $db = Database::getInstance();

        // Cours non affectés à une classe : affichage de tous les cours avec leur UE
        $courses = $db->query("SELECT * FROM cours ORDER BY ue_nom, libelle")->fetchAll();

        // Classes avec cours affectés (via affectation_cours)
        $classesCours = $db->query("
            SELECT DISTINCT cl.id_classe, cl.nom_classe, cl.niveau,
                   aa.libelle AS annee_libelle
            FROM classe cl
            JOIN affectation_cours ac ON cl.id_classe = ac.fk_classe
            JOIN annee_academique aa ON cl.fk_annee = aa.id_annee
            ORDER BY aa.libelle DESC, cl.nom_classe
        ")->fetchAll();

        // Pour chaque classe, récupérer ses cours groupés par UE
        $coursesByClass = [];
        foreach ($classesCours as $cl) {
            $stmt = $db->prepare("
                SELECT co.*, p.nom AS prof_nom, p.prenom AS prof_prenom
                FROM cours co
                JOIN affectation_cours ac ON co.id_cours = ac.fk_cours
                LEFT JOIN professeur p ON ac.fk_prof = p.id_prof
                WHERE ac.fk_classe = ?
                ORDER BY co.ue_nom, co.libelle
            ");
            $stmt->execute([$cl->id_classe]);
            $coursesByClass[$cl->id_classe] = $stmt->fetchAll();
        }

        $this->view('course/index', [
            'courses' => $courses,
            'classesCours' => $classesCours,
            'coursesByClass' => $coursesByClass
        ]);
    }

    public function create() {
        $db = Database::getInstance();
        $professors = $db->query("SELECT * FROM professeur ORDER BY nom, prenom")->fetchAll();
        
        if ($_SESSION['role'] === 'responsable') {
            $id_classe = $_SESSION['id_classe'] ?? null;
            $classes = $db->prepare("SELECT * FROM classe WHERE id_classe = ?");
            $classes->execute([$id_classe]);
            $classes = $classes->fetchAll();
        } else {
            $classes = $db->query("SELECT * FROM classe ORDER BY nom_classe")->fetchAll();
        }

        $this->view('course/form', [
            'action' => 'store',
            'title' => 'Ajouter un Cours/Matière',
            'course' => null,
            'professors' => $professors,
            'classes' => $classes
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $ue = Security::escape($_POST['ue_nom']);
            $nom = Security::escape($_POST['libelle']);
            $coeff = (float) $_POST['coefficient'];
            
            $stmt = $db->prepare("INSERT INTO cours (ue_nom, libelle, coefficient) VALUES (?, ?, ?)");
            $stmt->execute([$ue, $nom, $coeff]);
            $course_id = $db->lastInsertId();

            // Affectation optionnelle
            $fk_prof = $_POST['fk_prof'] ?? null;
            $fk_classe = $_POST['fk_classe'] ?? null;
            $stmtYear = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1");
            $year = $stmtYear->fetch();

            if ($year && $fk_prof && $fk_classe) {
                $stmtAff = $db->prepare("INSERT INTO affectation_cours (fk_prof, fk_cours, fk_classe, fk_annee) VALUES (?, ?, ?, ?)");
                $stmtAff->execute([$fk_prof, $course_id, $fk_classe, $year->id_annee]);
            }

            $this->redirect('course');
        }
    }

    public function edit($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT co.*, ac.fk_prof, ac.fk_classe 
            FROM cours co
            LEFT JOIN affectation_cours ac ON co.id_cours = ac.fk_cours AND ac.fk_annee = (SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1)
            WHERE co.id_cours = ?
        ");
        $stmt->execute([$id]);
        $c = $stmt->fetch();

        $professors = $db->query("SELECT * FROM professeur ORDER BY nom, prenom")->fetchAll();
        
        if ($_SESSION['role'] === 'responsable') {
            $id_classe = $_SESSION['id_classe'] ?? null;
            $classes = $db->prepare("SELECT * FROM classe WHERE id_classe = ?");
            $classes->execute([$id_classe]);
            $classes = $classes->fetchAll();
        } else {
            $classes = $db->query("SELECT * FROM classe ORDER BY nom_classe")->fetchAll();
        }

        if ($c) {
            $this->view('course/form', [
                'action' => 'update/' . $id,
                'title' => 'Modifier le Cours',
                'course' => $c,
                'professors' => $professors,
                'classes' => $classes
            ]);
        } else {
            $this->redirect('course');
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $ue = Security::escape($_POST['ue_nom']);
            $nom = Security::escape($_POST['libelle']);
            $coeff = (float) $_POST['coefficient'];

            $stmt = $db->prepare("UPDATE cours SET ue_nom = ?, libelle = ?, coefficient = ? WHERE id_cours = ?");
            $stmt->execute([$ue, $nom, $coeff, $id]);

            // Mise à jour de l'affectation
            $fk_prof = $_POST['fk_prof'] ?? null;
            $fk_classe = $_POST['fk_classe'] ?? null;
            $stmtYear = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1");
            $year = $stmtYear->fetch();

            if ($year && $fk_prof && $fk_classe) {
                // Check existante pour ce cours + classe (on suppose 1 prof par cours par classe)
                $chk = $db->prepare("SELECT id_affectation FROM affectation_cours WHERE fk_cours = ? AND fk_classe = ? AND fk_annee = ?");
                $chk->execute([$id, $fk_classe, $year->id_annee]);
                $affectation = $chk->fetch();

                if($affectation) {
                    $upd = $db->prepare("UPDATE affectation_cours SET fk_prof = ? WHERE id_affectation = ?");
                    $upd->execute([$fk_prof, $affectation->id_affectation]);
                } else {
                    $ins = $db->prepare("INSERT INTO affectation_cours (fk_prof, fk_cours, fk_classe, fk_annee) VALUES (?, ?, ?, ?)");
                    $ins->execute([$fk_prof, $id, $fk_classe, $year->id_annee]);
                }
            }

            $this->redirect('course');
        }
    }

    public function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM cours WHERE id_cours = ?");
        $stmt->execute([$id]);
        $this->redirect('course');
    }
}
