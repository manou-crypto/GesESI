<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php?url=grade/professor_list">Mes Cours</a></li>
                <li class="breadcrumb-item active">Saisie des Notes</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border-start border-danger border-5">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--primary-color);"><?php echo htmlspecialchars($assignment->cours_nom); ?></h3>
                <p class="text-muted mb-0">Classe : <strong><?php echo htmlspecialchars($assignment->nom_classe); ?></strong></p>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="badge bg-danger-subtle text-danger px-3 py-2 border border-danger-subtle">
                    <i class="fas fa-edit me-1"></i> Nouvelle Session de Notes
                </span>
            </div>
        </div>
    </div>
</div>

<?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> Les notes ont été enregistrées avec succès !
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?php echo BASE_URL; ?>/index.php?url=grade/save_bulk" method="POST">
    <input type="hidden" name="id_cours" value="<?php echo $assignment->fk_cours; ?>">
    <input type="hidden" name="id_affectation" value="<?php echo $assignment->id_affectation; ?>">

    <!-- Paramètres de l'évaluation -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-info-circle text-danger me-2"></i>Détails de l'évaluation</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Type ou Nom de l'évaluation <span class="text-danger">*</span></label>
                    <input type="text" name="type_evaluation" class="form-control" placeholder="Ex: Devoir 1, Interrogation, Partiel..." required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Coefficient <span class="text-danger">*</span></label>
                    <input type="number" step="0.5" name="coefficient" class="form-control" value="1.0" required>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <p class="text-muted small mb-2 text-italic">Ce coefficient s'appliquera uniquement à cette série de notes.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Étudiant</th>
                            <th class="py-3 text-center" style="width: 200px;">Note ( / 20 )</th>
                            <th class="pe-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $s): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?php echo htmlspecialchars($s->nom . ' ' . $s->prenom); ?></div>
                                <small class="text-muted">Inscrire la note pour cette nouvelle évaluation</small>
                            </td>
                            <td class="text-center">
                                <input type="number" step="0.25" min="0" max="20" 
                                       name="notes[<?php echo $s->id_etudiant; ?>]" 
                                       class="form-control text-center fw-bold border-2 mx-auto" 
                                       style="width: 100px; border-color: #ddd;"
                                       placeholder="-">
                            </td>
                            <td class="pe-4 text-end">
                                <i class="far fa-edit text-muted"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white p-4 border-top-0 d-flex justify-content-between align-items-center">
            <a href="<?php echo BASE_URL; ?>/index.php?url=grade/professor_list" class="btn btn-outline-secondary px-4">Annuler</a>
            <button type="submit" class="btn btn-danger px-5 py-2 fw-bold shadow-sm">
                <i class="fas fa-save me-2"></i> Enregistrer cette série de notes
            </button>
        </div>
    </div>
</form>

<?php if(!empty($evaluations)): ?>
<div class="mt-5">
    <h5 class="fw-bold mb-3"><i class="fas fa-history text-muted me-2"></i>Historique des évaluations pour ce cours</h5>
    <div class="row">
        <?php foreach($evaluations as $eval): ?>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 border-top border-danger border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-danger"><?php echo htmlspecialchars($eval->type_evaluation); ?></span>
                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($eval->date_evaluation)); ?></small>
                    </div>
                    <h6 class="fw-bold mb-1">Coefficient : <?php echo $eval->coefficient; ?></h6>
                    <p class="small text-muted mb-0">Notes enregistrées pour toute la classe.</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once 'app/Views/layout/footer.php'; ?>
