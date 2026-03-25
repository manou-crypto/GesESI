<?php require_once 'app/Views/layout/header.php'; ?>

<!-- Content Header -->
<div class="row">
    <div class="col-12 bg-warning text-dark p-3 rounded d-flex align-items-center mb-4 mt-3" style="background: linear-gradient(90deg, #f1c40f, #f39c12);">
        <h2 class="m-0 text-white"><i class="fas fa-edit me-3"></i> Saisie des Notes d'Évaluation</h2>
        <span class="badge bg-dark ms-auto">Année Académique Active</span>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success mt-3"><i class="fas fa-check-circle"></i> Notes enregistrées et verrouillées avec succès.</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pb-0">
                <form class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold">Sélectionner la classe</label>
                        <select class="form-select border-primary" disabled>
                            <option selected>L1 Informatique (Actuelle)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold">Sélectionner le cours</label>
                        <select class="form-select border-warning">
                            <option value="1">Algèbre (Coef 4)</option>
                            <option value="2">Analyse (Coef 4)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Charger la liste</button>
                    </div>
                </form>
            </div>
            <div class="card-body mt-3">
                <form action="<?php echo BASE_URL; ?>/index.php?url=grades/save" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="cours_id" value="1">

                    <table class="table table-hover table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Matricule</th>
                                <th class="text-start">Étudiant</th>
                                <th style="width: 15%">Note sur 20</th>
                                <th>Appréciation (Optionnel)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach($students as $s): ?>
                                <tr>
                                    <td class="text-muted"><?php echo \App\Core\Security::escape($s->matricule); ?></td>
                                    <td class="text-start fw-bold"><?php echo \App\Core\Security::escape($s->nom . ' ' . $s->prenom); ?></td>
                                    <td>
                                        <input type="number" step="0.25" min="0" max="20" class="form-control text-center" name="notes[<?php echo $s->id_etudiant; ?>]" placeholder="--">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Observations...">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">Aucun étudiant n'est listé.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5 shadow"><i class="fas fa-save"></i> Enregistrer les notes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>
