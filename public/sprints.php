<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$data = loadData();
$pageTitle = 'Equips';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-primary fw-semibold mb-1"></p>
        <h1>Sprints</h1>
    </div>
</div>

<div class="row g-3">
    <?php foreach ($data['sprints'] ?? [] as $sprint): ?>
        <?php
        ?>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h4"><?= h($sprint['name']) ?></h2>
                    <p class="text-muted">
                        <?= h($sprint['start_date'] ?? '') ?> - <?= h($sprint['end_date'] ?? '') ?>
                    </p>
                    <a class="btn btn-outline-primary" href="team.php?id=<?= $sprint['id'] ?>">
                        Veure fitxa
                    </a>
                    <span class="ms-2">Objectiu: <?= h($sprint['goal'] ?? '') ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>

