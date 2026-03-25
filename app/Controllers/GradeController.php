<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class GradeController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        Security::requireRole(['super_admin', 'responsable', 'professeur', 'etudiant']);
    }

    /**
     * Espace Enseignant : Liste des classes et cours assignés
     */
    public function professor_list() {
        Security::requireRole(['professeur', 'super_admin', 'responsable']);
        
        $db = Database::getInstance();
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];

        $sql = "
            SELECT ac.id_affectation, c.libelle as cours_nom, c.id_cours, cl.nom_classe, cl.id_classe, cl.niveau
            FROM affectation_cours ac
            JOIN cours c ON ac.fk_cours = c.id_cours
            JOIN classe cl ON ac.fk_classe = cl.id_classe
            JOIN professeur p ON ac.fk_prof = p.id_prof
            JOIN annee_academique aa ON ac.fk_annee = aa.id_annee
            WHERE aa.est_active = 1
        ";

        if ($role === 'professeur') {
            $sql .= " AND p.fk_user = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$user_id]);
        } else {
            $stmt = $db->query($sql);
        }

        $assignments = $stmt->fetchAll();
        $this->view('grade/professor_list', ['assignments' => $assignments]);
    }

    /**
     * Saisie des notes pour une affectation spécifique
     */
    public function enter($id_affectation) {
        Security::requireRole(['professeur', 'super_admin', 'responsable']);
        
        $db = Database::getInstance();
        
        // 1. Récupérer les détails de l'affectation
        $stmt = $db->prepare("
            SELECT ac.*, c.libelle as cours_nom, cl.nom_classe 
            FROM affectation_cours ac
            JOIN cours c ON ac.fk_cours = c.id_cours
            JOIN classe cl ON ac.fk_classe = cl.id_classe
            WHERE ac.id_affectation = ?
        ");
        $stmt->execute([$id_affectation]);
        $assignment = $stmt->fetch();

        if (!$assignment) $this->redirect('grade/professor_list');

        // 2. Récupérer les étudiants inscrits dans cette classe pour l'année active
        $stmt2 = $db->prepare("
            SELECT e.id_etudiant, e.nom, e.prenom, n.valeur, n.id_note
            FROM inscription i
            JOIN etudiant e ON i.fk_etudiant = e.id_etudiant
            LEFT JOIN note n ON n.fk_etudiant = e.id_etudiant AND n.fk_cours = ? AND n.fk_annee = i.fk_annee
            WHERE i.fk_classe = ? AND i.fk_annee = ?
            ORDER BY e.nom, e.prenom
        ");
        $stmt2->execute([$assignment->fk_cours, $assignment->fk_classe, $assignment->fk_annee]);
        $students = $stmt2->fetchAll();

        // 3. Récupérer les types d'évaluations déjà existants pour ce cours (pour information)
        $stmt3 = $db->prepare("
            SELECT DISTINCT type_evaluation, coefficient, date_evaluation 
            FROM note 
            WHERE fk_cours = ? AND fk_annee = ?
            ORDER BY date_evaluation DESC
        ");
        $stmt3->execute([$assignment->fk_cours, $assignment->fk_annee]);
        $evaluations = $stmt3->fetchAll();

        $this->view('grade/entry_form', [
            'assignment' => $assignment,
            'students' => $students,
            'evaluations' => $evaluations
        ]);
    }

    /**
     * Sauvegarde en masse des notes
     */
    public function save_bulk() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $id_cours = $_POST['id_cours'];
            $id_affectation = $_POST['id_affectation'];
            $type_evaluation = Security::escape($_POST['type_evaluation'] ?? 'Devoir');
            $coefficient = floatval($_POST['coefficient'] ?? 1.0);
            
            // Get active academic year
            $stmt_year = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1");
            $year = $stmt_year->fetch();

            if (!$year) {
                $this->redirect('grade/enter/' . $id_affectation . '&status=error');
                return;
            }

            foreach ($_POST['notes'] as $id_etudiant => $valeur) {
                if ($valeur === '') continue;

                // On insère TOUJOURS une nouvelle note pour permettre d'en avoir plusieurs
                $ins = $db->prepare("
                    INSERT INTO note (fk_etudiant, fk_cours, fk_annee, valeur, type_evaluation, coefficient, date_evaluation) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $ins->execute([$id_etudiant, $id_cours, $year->id_annee, $valeur, $type_evaluation, $coefficient]);
            }

            $this->redirect('grade/enter/' . $id_affectation . '&status=success');
        }
    }

    /**
     * Espace Étudiant : Mes notes
     */
    public function student_view() {
        Security::requireRole(['etudiant', 'super_admin', 'responsable']);
        
        $db = Database::getInstance();
        $user_id = $_SESSION['user_id'];
        
        // Trouver l'étudiant
        $stmtE = $db->prepare("SELECT id_etudiant FROM etudiant WHERE fk_user = ?");
        $stmtE->execute([$user_id]);
        $etudiant = $stmtE->fetch();

        if (!$etudiant) die("Profil étudiant non trouvé.");

        // Get active academic year
        $stmt_year = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1");
        $year = $stmt_year->fetch();
        if (!$year) {
            die("No active academic year found.");
        }

        // Récupérer les notes pour l'année active
        $stmt = $db->prepare("
            SELECT c.libelle, c.ue_nom, c.coefficient, n.valeur, n.date_evaluation
            FROM note n
            JOIN cours c ON n.fk_cours = c.id_cours
            WHERE n.fk_etudiant = ? AND n.fk_annee = ?
            ORDER BY c.ue_nom, c.libelle
        ");
        $stmt->execute([$etudiant->id_etudiant, $year->id_annee]);
        $grades = $stmt->fetchAll();

        $this->view('grade/student_view', ['grades' => $grades]);
    }
}
