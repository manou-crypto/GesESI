<?php 
$current_url = $_GET['url'] ?? 'dashboard';
$role = $_SESSION['role'] ?? 'etudiant';
?>
<aside id="sidebar" class="shadow-sm">
    <div class="sidebar-header p-4 text-center border-bottom border-white border-opacity-25">
        <h4 class="text-white fw-bold mb-0"><i class="fas fa-graduation-cap me-2"></i><?php echo APP_NAME; ?></h4>
    </div>
    
    <div class="sidebar-menu p-3">
        <small class="text-white text-opacity-75 text-uppercase fw-bold mb-2 d-block px-3" style="font-size: 0.7rem;">Menu Principal</small>
        
        <a href="<?php echo BASE_URL; ?>/index.php?url=dashboard" class="menu-item <?php echo $current_url == 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line me-3"></i> Tableau de Bord
        </a>

        <?php if ($role === 'professeur'): ?>
            <a href="<?php echo BASE_URL; ?>/index.php?url=grade/professor_list" class="menu-item <?php echo strpos($current_url, 'grade/professor') !== false ? 'active' : ''; ?>">
                <i class="fas fa-edit me-3"></i> Mes Enseignements
            </a>
        <?php endif; ?>

        <?php if ($role === 'etudiant'): ?>
            <a href="<?php echo BASE_URL; ?>/index.php?url=grade/student_view" class="menu-item <?php echo strpos($current_url, 'grade/student') !== false ? 'active' : ''; ?>">
                <i class="fas fa-award me-3"></i> Mes Notes & Résultats
            </a>
        <?php endif; ?>

        <?php if (in_array($role, ['super_admin', 'responsable'])): ?>
            <a href="<?php echo BASE_URL; ?>/index.php?url=student/index" class="menu-item <?php echo strpos($current_url, 'student') !== false ? 'active' : ''; ?>">
                <i class="fas fa-user-graduate me-3"></i> Liste Étudiants
            </a>
            <a href="<?php echo BASE_URL; ?>/index.php?url=teacher/index" class="menu-item <?php echo strpos($current_url, 'teacher') !== false ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard-teacher me-3"></i> Corps Enseignant
            </a>
            <?php if ($role === 'super_admin'): ?>
                <a href="<?php echo BASE_URL; ?>/index.php?url=course/index" class="menu-item <?php echo strpos($current_url, 'course') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-book me-3"></i> Gestion des Cours
                </a>
                <a href="<?php echo BASE_URL; ?>/index.php?url=class/index" class="menu-item <?php echo strpos($current_url, 'class') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-school me-3"></i> Classes & Filières
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($role === 'super_admin'): ?>
            <small class="text-white text-opacity-75 text-uppercase fw-bold mt-4 mb-2 d-block px-3" style="font-size: 0.7rem;">Administration</small>
            <a href="<?php echo BASE_URL; ?>/index.php?url=staff/index" class="menu-item <?php echo strpos($current_url, 'staff') !== false ? 'active' : ''; ?>">
                <i class="fas fa-user-shield me-3"></i> Gestion Staff
            </a>
            <a href="<?php echo BASE_URL; ?>/index.php?url=year/index" class="menu-item <?php echo strpos($current_url, 'year') !== false ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt me-3"></i> Années Académiques
            </a>
        <?php endif; ?>

        <?php if (in_array($role, ['super_admin', 'responsable'])): ?>
            <small class="text-white text-opacity-75 text-uppercase fw-bold mt-4 mb-2 d-block px-3" style="font-size: 0.7rem;">Rapports</small>
            <a href="<?php echo BASE_URL; ?>/index.php?url=report/bilan" class="menu-item <?php echo strpos($current_url, 'report') !== false ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice me-3"></i> Planche de Notes
            </a>
        <?php endif; ?>

        <div class="mt-5 pt-5 border-top border-white border-opacity-25">
            <a href="<?php echo BASE_URL; ?>/index.php?url=auth/logout" class="menu-item text-white-50">
                <i class="fas fa-sign-out-alt me-3"></i> Déconnexion
            </a>
        </div>
    </div>
</aside>

<style>
.sidebar {
    width: 260px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
    z-index: 1000;
}
.menu-item {
    display: flex;
    align-items: center;
    padding: 12px 18px;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    border-radius: 10px;
    margin-bottom: 5px;
    transition: all 0.3s;
    font-weight: 500;
    font-size: 0.95rem;
}
.menu-item:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
}
.menu-item.active {
    background: #fff;
    color: #00BFFF;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
.menu-item i {
    width: 20px;
    text-align: center;
}
</style>
