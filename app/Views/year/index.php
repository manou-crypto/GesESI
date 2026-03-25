<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row">
    <div class="col-12 text-white p-4 rounded d-flex align-items-center mb-4 mt-3 shadow-sm" style="background: linear-gradient(90deg, #00BFFF, #87CEEB);">
        <h2 class="m-0"><i class="fas fa-calendar-alt me-3"></i> Années Académiques</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pb-0 pt-3">
                <h5 class="fw-bold text-muted mb-3"><i class="fas fa-history"></i> Historique des années</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Libellé (ex: 2024-2025)</th>
                            <th>Statut</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($years as $year): ?>
                        <tr>
                            <td class="text-muted">#<?php echo $year->id_annee; ?></td>
                            <td class="fw-bold fs-5"><?php echo \App\Core\Security::escape($year->libelle); ?></td>
                            <td>
                                <?php if($year->est_active == 1): ?>
                                    <span class="badge bg-success px-3 py-2">En cours (Active)</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary px-3 py-2">Clôturée</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if($year->est_active != 1): ?>
                                    <button class="btn btn-sm btn-outline-success" onclick="alert('Fonctionnalité de bascule d\'année académique non activée dans cette démo.')"><i class="fas fa-check"></i> Activer</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-success" disabled><i class="fas fa-asterisk"></i> Active</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                 <button class="btn btn-primary mt-3 w-100 disabled text-white"><i class="fas fa-plus-circle"></i> Initier une nouvelle année académique (Bientôt disponible)</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>
