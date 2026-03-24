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
        $this->view('course/form', [
            'action' => 'store',
            'title' => 'Ajouter un Cours/Matière',
            'course' => null
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $ue = Security::escape($_POST['ue_nom']);
            $nom = Security::escape($_POST['libelle']);
            $coeff = (int) $_POST['coefficient'];
            
            $stmt = $db->prepare("INSERT INTO cours (ue_nom, libelle, coefficient) VALUES (?, ?, ?)");
            $stmt->execute([$ue, $nom, $coeff]);
            $this->redirect('course');
        }
    }

    public function edit($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM cours WHERE id_cours = ?");
        $stmt->execute([$id]);
        $c = $stmt->fetch();

        if ($c) {
            $this->view('course/form', [
                'action' => 'update/' . $id,
                'title' => 'Modifier le Cours',
                'course' => $c
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
            $coeff = (int) $_POST['coefficient'];

            $stmt = $db->prepare("UPDATE cours SET ue_nom = ?, libelle = ?, coefficient = ? WHERE id_cours = ?");
            $stmt->execute([$ue, $nom, $coeff, $id]);
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
