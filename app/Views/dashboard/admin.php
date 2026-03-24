<?php require_once '../app/Views/layout/header.php'; ?>

<!-- Content Header -->
<div class="row mb-4">
    <div class="col-12 text-white p-4 rounded d-flex align-items-center my-3 shadow-sm" style="background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));">
        <h2 class="m-0"><i class="fas fa-home me-3"></i> Bienvenue sur votre espace de gestion</h2>
    </div>
</div>

<!-- Stats (Dynamique reliée à la base de données) -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-stat bg-white text-center p-4">
            <h1 class="fw-bold" style="color: var(--primary-color);"><i class="fas fa-user-graduate"></i> <?php echo number_format($stats['total_students']); ?></h1>
            <p class="text-muted m-0">Étudiants Inscrits</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat bg-white text-center p-4">
            <h1 class="text-success fw-bold"><i class="fas fa-chalkboard-teacher"></i> <?php echo number_format($stats['total_teachers']); ?></h1>
            <p class="text-muted m-0">Enseignants Affectés</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat bg-white text-center p-4">
            <h1 class="text-warning fw-bold"><i class="fas fa-book"></i> <?php echo number_format($stats['total_courses']); ?></h1>
            <p class="text-muted m-0">Cours / ECUEs</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat bg-white text-center p-4">
            <h2 class="text-danger fw-bold mt-2"><i class="fas fa-calendar-check mt-1"></i> <?php echo htmlspecialchars($stats['active_year']); ?></h2>
            <p class="text-muted m-0">Année Académique Active</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card table-custom">
            <div class="card-body text-center p-5">
                <i class="fas fa-users-cog fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Gérez les données depuis le menu à gauche</h4>
                <p class="text-muted small">Sélectionnez une rubrique pour commencer (Étudiants, Professeurs, Cours...)</p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
