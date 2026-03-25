<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0"><i class="fas fa-user-shield text-danger me-2"></i> Gestion du Staff Personnel</h2>
        <p class="text-muted small mb-0">Administrateurs et Responsables ayant accès à la plateforme</p>
    </div>
    <div class="col-auto">
        <a href="<?php echo BASE_URL; ?>/index.php?url=staff/create" class="btn btn-danger">
            <i class="fas fa-plus-circle me-1"></i> Ajouter un Membre
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Identité</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date Création</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($staff as $member): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <?php echo strtoupper(substr($member->nom, 0, 1) . substr($member->prenom, 0, 1)); ?>
                                </div>
                                <div class="fw-bold"><?php echo htmlspecialchars($member->nom . ' ' . $member->prenom); ?></div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($member->email); ?></td>
                        <td>
                            <?php if($member->role === 'super_admin'): ?>
                                <span class="badge bg-danger">Super Admin</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Responsable</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?php echo date('d/m/Y', strtotime($member->date_creation)); ?></td>
                        <td class="text-center">
                            <a href="<?php echo BASE_URL; ?>/index.php?url=staff/edit/<?php echo $member->id_staff; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <a href="<?php echo BASE_URL; ?>/index.php?url=staff/delete/<?php echo $member->id_staff; ?>" 
                               onclick="return confirm('Supprimer ce compte ? Cette action est irréversible.');" 
                               class="btn btn-sm btn-outline-danger ms-1"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>
