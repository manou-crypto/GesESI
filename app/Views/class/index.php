<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row">
    <div class="col-12 text-white p-4 rounded d-flex align-items-center mb-4 mt-3 shadow-sm" style="background: linear-gradient(90deg, #00BFFF, #87CEEB);">
        <h2 class="m-0"><i class="fas fa-layer-group me-3"></i> Gestion des Classes & Filières</h2>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <a href="<?php echo BASE_URL; ?>/index.php?url=class/create" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Ajouter une Classe</a>
        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table table-hover table-custom mb-0 align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Nom de la Classe</th>
                        <th>Niveau / Filière</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classes)): ?>
                        <?php foreach($classes as $c): ?>
                        <tr>
                            <td class="fw-bold fs-5 text-primary"><?php echo \App\Core\Security::escape($c->nom_classe); ?></td>
                            <td><span class="badge bg-secondary"><?php echo \App\Core\Security::escape($c->niveau); ?></span></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/index.php?url=class/edit/<?php echo $c->id_classe; ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-edit"></i> Modifier</a>
                                <a href="<?php echo BASE_URL; ?>/index.php?url=class/delete/<?php echo $c->id_classe; ?>" onclick="return confirm('Supprimer cette classe supprimera également les inscriptions liées ! Continuer ?');" class="btn btn-sm btn-outline-danger ms-1"><i class="fas fa-trash"></i> Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Aucune classe enregistrée.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
