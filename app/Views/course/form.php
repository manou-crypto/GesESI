<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto mt-4">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header text-dark font-weight-bold py-3 shadow-sm" style="background: linear-gradient(90deg, #FFD700, #F0E68C);">
                <h4 class="m-0"><i class="fas fa-book"></i> <?php echo htmlspecialchars($title ?? 'Formulaire Matière'); ?></h4>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/index.php?url=course/<?php echo $action; ?>" method="POST">
                    <div class="row mb-5">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Unité d'Enseignement (UE) <span class="text-danger">*</span></label>
                            <input type="text" name="ue_nom" class="form-control" value="<?php echo isset($course) ? \App\Core\Security::escape($course->ue_nom) : ''; ?>" required placeholder="ex: UE1 : Mathématiques">
                        </div>
                        <div class="col-md-8">
                            <label>Nom de la matière (ECUE) <span class="text-danger">*</span></label>
                            <input type="text" name="libelle" class="form-control" value="<?php echo isset($course) ? \App\Core\Security::escape($course->libelle) : ''; ?>" required placeholder="ex: Mathématiques Avancées">
                        </div>
                        <div class="col-md-4">
                            <label>Coefficient <span class="text-danger">*</span></label>
                            <input type="number" name="coefficient" class="form-control" value="<?php echo isset($course) ? (int)$course->coefficient : '1'; ?>" min="1" required>
                        </div>
                    </div>

                    <button class="btn btn-warning text-dark fw-bold px-4 py-2"><i class="fas fa-save"></i> Enregistrer le cours</button>
                    <a href="<?php echo BASE_URL; ?>/index.php?url=course" class="btn btn-outline-secondary px-4 py-2 ms-2">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
