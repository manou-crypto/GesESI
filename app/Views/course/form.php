<?php require_once 'app/Views/layout/header.php'; ?>

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
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nom de la matière (ECUE) <span class="text-danger">*</span></label>
                            <input type="text" name="libelle" class="form-control" value="<?php echo isset($course) ? \App\Core\Security::escape($course->libelle) : ''; ?>" required placeholder="ex: Mathématiques Avancées">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Coefficient <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" name="coefficient" class="form-control" value="<?php echo isset($course) ? (float)$course->coefficient : '1.0'; ?>" min="0.5" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-success"><i class="fas fa-chalkboard-teacher me-1"></i> Enseignant Responsable</label>
                            <select name="fk_prof" class="form-select border-success">
                                <option value="">Choisir un professeur...</option>
                                <?php foreach($professors as $p): ?>
                                    <option value="<?php echo $p->id_prof; ?>" <?php echo (isset($course->fk_prof) && $course->fk_prof == $p->id_prof) ? 'selected' : ''; ?>>
                                        <?php echo \App\Core\Security::escape($p->nom . ' ' . $p->prenom); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-primary"><i class="fas fa-school me-1"></i> Classe d'Affectation</label>
                            <select name="fk_classe" class="form-select border-primary" <?php echo ($_SESSION['role'] === 'responsable') ? 'readonly' : ''; ?>>
                                <option value="">Choisir une classe...</option>
                                <?php foreach($classes as $cl): ?>
                                    <option value="<?php echo $cl->id_classe; ?>" <?php echo (isset($course->fk_classe) && $course->fk_classe == $cl->id_classe) ? 'selected' : ''; ?> <?php echo ($_SESSION['role'] === 'responsable' && $cl->id_classe == $_SESSION['id_classe']) ? 'selected' : ''; ?>>
                                        <?php echo \App\Core\Security::escape($cl->nom_classe); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if($_SESSION['role'] === 'responsable'): ?>
                                <input type="hidden" name="fk_classe" value="<?php echo $_SESSION['id_classe']; ?>">
                            <?php endif; ?>
                        </div>
                    </div>

                    <button class="btn btn-warning text-dark fw-bold px-4 py-2"><i class="fas fa-save"></i> Enregistrer le cours</button>
                    <a href="<?php echo BASE_URL; ?>/index.php?url=course" class="btn btn-outline-secondary px-4 py-2 ms-2">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>
