<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar incluse dynamiquement -->
        <?php require_once 'app/Views/layout/sidebar.php'; ?>
        
        <!-- Page Content -->
        <div id="content">
            <!-- Navbar de la page -->
            <nav class="navbar-custom d-flex justify-content-between align-items-center bg-white shadow-sm border-bottom px-4" style="height: 70px;">
                <div class="d-flex align-items-center">
                    <button id="sidebarCollapse" class="btn btn-outline-danger me-3"><i class="fas fa-bars"></i></button>
                    <h5 class="m-0 fw-bold" style="color: var(--primary-color);">Tableau de Bord Académique</h5>
                </div>
                <div class="top-menu d-flex align-items-center">
                    <div id="themeSwitch" class="me-4 text-primary" style="cursor: pointer;">
                        <i class="fas fa-moon"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-3 text-muted small px-2 d-none d-md-inline"><i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['email'] ?? 'Utilisateur'; ?></span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 text-uppercase" style="font-size: 0.7rem;">
                            <?php 
                                $roles = ['super_admin' => 'Admin Boss', 'responsable' => 'Responsable', 'professeur' => 'Enseignant', 'etudiant' => 'Étudiant'];
                                echo $roles[$_SESSION['role']] ?? $_SESSION['role'];
                            ?>
                        </span>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid py-4 p-md-5">
