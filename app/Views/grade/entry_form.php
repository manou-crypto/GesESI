<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php?url=grade/professor_list">Mes Cours</a></li>
                <li class="breadcrumb-item active">Saisie des Notes</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm border-start border-primary border-5">
            <div>
                <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($assignment->cours_nom); ?></h3>
                <p class="text-muted mb-0">Classe : <strong><?php echo htmlspecialchars($assignment->nom_classe); ?></strong> | Année : 2024-2025</p>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="badge bg-success-subtle text-success px-3 py-2 border border-success-subtle">
                    <i class="fas fa-check-circle me-1"></i> Session Ouverte
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

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3" style="width: 300px;">Étudiant</th>
                            <th class="py-3 text-center" style="width: 150px;">Note ( / 20 )</th>
                            <th class="py-3">Observation</th>
                            <th class="pe-4 py-3 text-end">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $s): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?php echo htmlspecialchars($s->nom . ' ' . $s->prenom); ?></div>
                                <small class="text-muted">Inscrit le <?php echo date('d/m/Y'); ?></small>
                            </td>
                            <td class="text-center">
                                <input type="number" step="0.25" min="0" max="20" 
                                       name="notes[<?php echo $s->id_etudiant; ?>]" 
                                       class="form-control text-center fw-bold border-2 mx-auto" 
                                       style="width: 80px; <?php echo $s->valeur !== null ? 'border-color: #00BFFF;' : ''; ?>"
                                       value="<?php echo $s->valeur; ?>">
                            </td>
                             <td>
                                <?php if($s->valeur !== null): ?>
                                    <?php if($s->valeur < 10) echo '<span class="text-danger small"><i class="fas fa-exclamation-triangle me-1"></i>En dessous de la moyenne</span>'; 
                                          else echo '<span class="text-success small"><i class="fas fa-check me-1"></i>Validé</span>'; ?>
                                <?php else: ?>
                                    <span class="text-muted small italic">Non saisie</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <?php if($s->valeur !== null): ?>
                                    <i class="fas fa-check-circle text-success" title="Enregistré"></i>
                                <?php else: ?>
                                    <i class="far fa-circle text-muted" title="En attente"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white p-4 border-top-0 d-flex justify-content-between align-items-center">
            <p class="text-muted small mb-0"><i class="fas fa-info-circle me-2"></i> Pensez à enregistrer régulièrement vos modifications.</p>
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                <i class="fas fa-save me-2"></i> Enregistrer toutes les notes
            </button>
        </div>
    </div>
</form>

<?php require_once '../app/Views/layout/footer.php'; ?>
