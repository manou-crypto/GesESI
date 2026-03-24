<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class ReportController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        Security::requireRole(['super_admin', 'responsable']); 
    }

    public function bilan() {
        $db = Database::getInstance();
        $stmtY = $db->query("SELECT id_annee, libelle FROM annee_academique WHERE est_active = 1 LIMIT 1");
        $year = $stmtY->fetch();
        if(!$year) {
            die("Aucune année active configurée.");
        }

        $class_id = $_GET['classe'] ?? null;

        // Sécurité : Responsable ne gère que sa classe
        if ($_SESSION['role'] === 'responsable') {
            $class_id = $_SESSION['id_classe'] ?? null;
            if (!$class_id) die("Erreur : Aucune classe assignée à votre profil.");
        }

        if (!$class_id) {
            $classes = $db->query("SELECT * FROM classe ORDER BY nom_classe")->fetchAll();
            $this->view('report/selection', ['classes' => $classes, 'title' => 'Sélecteur de Planche']);
            return;
        }

        // Fetch all courses grouped by UE
        $coursesDb = $db->query("SELECT * FROM cours ORDER BY ue_nom, libelle")->fetchAll();
        $ues = [];
        foreach($coursesDb as $c) {
            if(!isset($ues[$c->ue_nom])) {
                $ues[$c->ue_nom] = ['courses' => [], 'total_coef' => 0];
            }
            $ues[$c->ue_nom]['courses'][] = $c;
            $ues[$c->ue_nom]['total_coef'] += $c->coefficient;
        }

        $studentsDb = $db->prepare("
            SELECT e.id_etudiant, e.matricule, e.nom, e.prenom, c.nom_classe 
            FROM etudiant e 
            JOIN inscription i ON e.id_etudiant = i.fk_etudiant AND i.fk_annee = ?
            JOIN classe c ON i.fk_classe = c.id_classe
            WHERE i.fk_classe = ?
            ORDER BY e.nom, e.prenom
        ");
        $studentsDb->execute([$year->id_annee, $class_id]);
        $students = $studentsDb->fetchAll();

        // Fetch all grades for this year and this class
        $gradesDb = $db->prepare("
            SELECT n.fk_etudiant, n.fk_cours, n.valeur 
            FROM note n 
            JOIN inscription i ON n.fk_etudiant = i.fk_etudiant AND i.fk_annee = ?
            WHERE i.fk_annee = ? AND i.fk_classe = ?
        ");
        $gradesDb->execute([$year->id_annee, $year->id_annee, $class_id]);
        $grades = $gradesDb->fetchAll();
        
        $gradesMap = []; 
        foreach($grades as $g) {
            $gradesMap[$g->fk_etudiant][$g->fk_cours] = $g->valeur;
        }

        $reportData = [];
        foreach($students as $st) {
            $stData = [
                'info' => $st,
                'grades' => [],
                'ue_averages' => [],
                'global_avg' => 0,
                'total_points' => 0,
                'total_coefs' => 0
            ];

            foreach($ues as $ueName => $ueData) {
                $uePoints = 0;
                $ueCoefs = 0;
                foreach($ueData['courses'] as $c) {
                    $grade = $gradesMap[$st->id_etudiant][$c->id_cours] ?? null;
                    $stData['grades'][$c->id_cours] = $grade;
                    if($grade !== null) {
                        $uePoints += ($grade * $c->coefficient);
                        $ueCoefs += $c->coefficient;
                    }
                }
                $ueAvg = $ueCoefs > 0 ? ($uePoints / $ueCoefs) : null;
                $stData['ue_averages'][$ueName] = $ueAvg;

                $stData['total_points'] += $uePoints;
                $stData['total_coefs'] += $ueCoefs;
            }

            $stData['global_avg'] = $stData['total_coefs'] > 0 ? ($stData['total_points'] / $stData['total_coefs']) : 0;
            $reportData[] = $stData;
        }

        // Sort by rank (descending global average)
        usort($reportData, function($a, $b) {
            return $b['global_avg'] <=> $a['global_avg'];
        });

        // Assign ranks
        $rank = 1;
        foreach($reportData as &$stData) {
            $stData['rank'] = $rank++;
        }

        // Calcul des moyennes de la classe pour chaque matière
        $classAverages = [];
        $classUeAverages = [];
        $globalClassPoints = 0;
        $globalClassCount = 0;
        
        foreach($ues as $ueName => $ueData) {
            $ueTotalPoints = 0;
            $ueTotalCount = 0;
            foreach($ueData['courses'] as $c) {
                $sum = 0;
                $count = 0;
                foreach($reportData as $stData) {
                    if($stData['grades'][$c->id_cours] !== null) {
                        $sum += $stData['grades'][$c->id_cours];
                        $count++;
                    }
                }
                $classAverages[$c->id_cours] = $count > 0 ? ($sum / $count) : null;
                
                if($classAverages[$c->id_cours] !== null) {
                    $ueTotalPoints += ($classAverages[$c->id_cours] * $c->coefficient);
                    $ueTotalCount += $c->coefficient;
                }
            }
            $classUeAverages[$ueName] = $ueTotalCount > 0 ? ($ueTotalPoints / $ueTotalCount) : null;
        }

        foreach($reportData as $stData) {
            $globalClassPoints += $stData['global_avg'];
            $globalClassCount++;
        }
        $globalClassAverage = $globalClassCount > 0 ? ($globalClassPoints / $globalClassCount) : 0;

        $this->view('report/bilan', [
            'annee' => $year->libelle,
            'ues' => $ues,
            'reportData' => $reportData,
            'classAverages' => $classAverages,
            'classUeAverages' => $classUeAverages,
            'globalClassAverage' => $globalClassAverage,
            'title' => 'Planche de Notes - Bilan Semestre'
        ]);
    }
}
