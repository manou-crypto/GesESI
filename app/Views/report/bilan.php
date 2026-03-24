<?php require_once '../app/Views/layout/header.php'; ?>

<style>
    .transcript-table th, .transcript-table td {
        vertical-align: middle;
        text-align: center;
        border: 1px solid #dee2e6;
        padding: 5px;
        font-size: 0.85rem;
    }
    .transcript-table .vertical-text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        height: 150px;
        white-space: nowrap;
        font-weight: bold;
    }
    .ue-header {
        background-color: #f1f2f6;
        font-weight: bold;
    }
    .ue-moyenne {
        background-color: #dfe6e9;
        font-weight: bold;
        color: #2d3436;
    }
    .global-stats {
        background-color: #bdc3c7;
        font-weight: bold;
    }
    .mention-val { color: #27ae60; font-weight: bold; }
    .mention-nval { color: #c0392b; font-weight: bold; }
</style>

<div class="row">
    <div class="col-12 bg-white text-dark p-4 rounded-4 shadow-sm mb-4 mt-3 border-start border-5 border-primary">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="m-0 fw-bold text-primary"><i class="fas fa-file-invoice me-2"></i> <?php echo htmlspecialchars($title ?? 'Planche de Notes'); ?></h3>
                <p class="text-muted m-0 mt-1">Année académique : <strong><?php echo htmlspecialchars($annee); ?></strong> | Session : <strong>Normale</strong></p>
                <p class="text-muted m-0"><small><em>Généré dynamiquement par le système GestESI</em></small></p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo BASE_URL; ?>/index.php?url=report/bilan" class="btn btn-outline-primary"><i class="fas fa-sync"></i> Autre Classe</a>
                <button class="btn btn-primary shadow-sm" onclick="window.print()"><i class="fas fa-print"></i> Imprimer la Planche</button>
            </div>
        </div>
    </div>
</div>

<div class="wrapper table-responsive bg-white rounded shadow-sm p-2 mb-5">
    <table class="table transcript-table table-bordered table-sm mw-100">
        <thead>
            <!-- Ligne des UEs -->
            <tr>
                <th colspan="3" rowspan="2" class="align-middle">Etudiant</th>
                <?php foreach($ues as $ueName => $ueData): ?>
                    <!-- Colonnes pour les matières de cette UE + colonnes extra (Moyenne UE) -->
                    <th colspan="<?php echo count($ueData['courses']) + 1; ?>" class="ue-header">
                        <?php echo htmlspecialchars($ueName); ?> <br>
                        <small class="text-muted">(Coef total: <?php echo $ueData['total_coef']; ?>)</small>
                    </th>
                <?php endforeach; ?>
                <th colspan="4" class="global-stats">Bilan du Semestre</th>
            </tr>
            <!-- Ligne des Matières Verticales -->
            <tr>
                <?php foreach($ues as $ueName => $ueData): ?>
                    <?php foreach($ueData['courses'] as $c): ?>
                        <th class="vertical-text" title="<?php echo htmlspecialchars($c->libelle); ?>">
                            <?php echo htmlspecialchars(mb_substr($c->libelle, 0, 40)); ?> <br>
                            <span class="badge bg-secondary p-1 mt-1">Coef <?php echo (float)$c->coefficient; ?></span>
                        </th>
                    <?php endforeach; ?>
                    <th class="vertical-text ue-moyenne text-danger">Moyenne <?php echo htmlspecialchars(explode(':', $ueName)[0] ?? 'UE'); ?></th>
                <?php endforeach; ?>
                
                <th class="vertical-text global-stats">Moyenne Générale</th>
                <th class="vertical-text global-stats">Rang</th>
                <th class="vertical-text global-stats">Décision du Jury</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($reportData as $stData): ?>
            <tr class="align-middle">
                <td><?php echo $stData['rank']; ?></td>
                <td class="text-start pe-2"><small class="text-muted"><?php echo htmlspecialchars($stData['info']->matricule); ?></small></td>
                <td class="text-start fw-bold text-nowrap"><?php echo htmlspecialchars($stData['info']->nom . ' ' . $stData['info']->prenom); ?></td>
                
                <?php foreach($ues as $ueName => $ueData): ?>
                    <?php foreach($ueData['courses'] as $c): ?>
                        <td><?php echo $stData['grades'][$c->id_cours] !== null ? number_format($stData['grades'][$c->id_cours], 2, '.', '') : '-'; ?></td>
                    <?php endforeach; ?>
                    <!-- Moyenne de l'UE pour cet étudiant -->
                    <td class="ue-moyenne"><?php echo $stData['ue_averages'][$ueName] !== null ? number_format($stData['ue_averages'][$ueName], 2, '.', '') : '-'; ?></td>
                <?php endforeach; ?>
                
                <td class="global-stats fs-6 text-primary"><?php echo number_format($stData['global_avg'], 2, '.', ''); ?></td>
                <td class="global-stats fw-bold"><?php echo $stData['rank']; ?></td>
                <td class="global-stats">
                    <?php if($stData['global_avg'] >= 10): ?>
                        <span class="mention-val">VAL</span>
                    <?php else: ?>
                        <span class="mention-nval">ENC / N-VAL</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>

            <!-- LIGNE DES MOYENNES DE CLASSE -->
            <tr class="align-middle bg-light fw-bold" style="border-top: 3px solid #34495e;">
                <td colspan="3" class="text-end pe-3 py-3 text-uppercase">Moyenne de la classe</td>
                <?php foreach($ues as $ueName => $ueData): ?>
                    <?php foreach($ueData['courses'] as $c): ?>
                        <td><?php echo $classAverages[$c->id_cours] !== null ? number_format($classAverages[$c->id_cours], 2, '.', '') : '-'; ?></td>
                    <?php endforeach; ?>
                    <td class="ue-moyenne"><?php echo $classUeAverages[$ueName] !== null ? number_format($classUeAverages[$ueName], 2, '.', '') : '-'; ?></td>
                <?php endforeach; ?>
                <td class="global-stats fs-5 text-dark"><?php echo number_format($globalClassAverage, 2, '.', ''); ?></td>
                <td colspan="2" class="global-stats"></td>
            </tr>
        </tbody>
    </table>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
