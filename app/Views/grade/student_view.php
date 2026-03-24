<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-12 bg-primary text-white p-4 rounded-4 d-flex align-items-center shadow-sm" style="background: linear-gradient(90deg, #00BFFF, #87CEEB);">
        <div class="me-4 bg-white p-3 rounded-circle shadow-sm">
            <i class="fas fa-user-graduate text-primary fa-2x"></i>
        </div>
        <div>
            <h2 class="m-0 fw-bold">Mon Espace de Notes</h2>
            <p class="mb-0 opacity-75">Consultez vos résultats pour l'année académique 2024-2025</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Relevé de Notes Détaillé</h5>
                <span class="badge bg-light text-dark border"><?php echo count($grades); ?> Matières renseignées</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">UE / Matière</th>
                                <th class="text-center">Coefficient</th>
                                <th class="text-center">Note / 20</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                             <?php 
                            $total_points = 0;
                            $total_coeffs = 0;
                            foreach($grades as $g): 
                                $total_points += ($g->valeur * $g->coefficient);
                                $total_coeffs += $g->coefficient;
                            ?>
                            <tr>
                                <td class="ps-4 text-dark">
                                    <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;"><?php echo htmlspecialchars($g->ue_nom); ?></div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($g->libelle); ?></div>
                                </td>
                                <td class="text-center fw-bold text-muted"><?php echo $g->coefficient; ?></td>
                                <td class="text-center">
                                    <span class="fs-5 fw-bold <?php echo $g->valeur >= 10 ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo number_format($g->valeur, 2); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if($g->valeur >= 10): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2">Validé</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">Échec</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            <div class="card-body p-4 text-center">
                <h6 class="text-muted text-uppercase fw-bold mb-3" style="letter-spacing: 1px;">Moyenne Générale</h6>
                <?php 
                $moyenne = $total_coeffs > 0 ? $total_points / $total_coeffs : 0;
                ?>
                <div class="display-3 fw-bold mb-2 <?php echo $moyenne >= 10 ? 'text-primary' : 'text-danger'; ?>">
                    <?php echo number_format($moyenne, 2); ?>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar <?php echo $moyenne >= 10 ? 'bg-primary' : 'bg-danger'; ?>" role="progressbar" style="width: <?php echo ($moyenne/20)*100; ?>%"></div>
                </div>
                <p class="text-muted small">
                    <?php if($moyenne >= 10): ?>
                        <i class="fas fa-check-circle text-success me-1"></i> Admis au semestre
                    <?php else: ?>
                        <i class="fas fa-times-circle text-danger me-1"></i> Ajourné
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-light">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Conseils Académiques</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><i class="fas fa-info-circle text-primary me-2"></i> Les notes définitives seront validées lors du jury de fin de semestre.</li>
                    <li><i class="fas fa-download text-primary me-2"></i> Téléchargez votre relevé provisoire au format PDF (Bientôt disponible).</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
