<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;
use App\Config\Database;

class YearController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        Security::requireRole(['super_admin', 'responsable']);
    }

    public function index() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM annee_academique ORDER BY id_annee DESC");
        $years = $stmt->fetchAll();

        $this->view('year/index', ['years' => $years]);
    }
}
