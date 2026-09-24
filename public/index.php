<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$data = loadData();
$user = currentUser();

// Creem la variable index visits i li assignem el valor de la sessio indexVisits
$indexVisits = ($_SESSION['indexVisits'] ?? 0) + 1;
$_SESSION['indexVisits'] = $indexVisits;
$tascas = array_splice($_SESSION['tasks'], 3);

$sprint = activeSprint($data['sprints'] ?? []);
$sprintTasks = array_filter(
    $data['tasks'] ?? [],
    static fn (array $task): bool => $sprint !== null
        && (int) ($task['sprint_id'] ?? 0) === (int) $sprint['id']
);
$counts = array_count_values(array_column($sprintTasks, 'status'));
$pageTitle = 'Inici';
require __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-primary fw-semibold mb-1">PANELL DE TREBALL</p>
        <h1>Hola, <?= h($user['name']. " " .$user[ 'email']) ?></h1>
    </div>
    <a class="btn btn-primary" href="sprints.php">+ Sprints</a>
    <a class="btn btn-primary" href="task-create.php">+ Nova tasca</a>
</div>

<?php if ($sprint): ?>
    <div class="card mb-4 border-primary">
        <div class="card-body">
            <span class="badge text-bg-success float-end">Actiu</span>
            <h2 class="h4"><?= h($sprint['name']) ?></h2>
            <p class="mb-1"><?= h($sprint['goal']) ?></p>
            <small class="text-muted">
                <?= h($sprint['start_date']) ?> → <?= h($sprint['end_date']) ?>
            </small>
        </div>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <?php foreach (['todo' => 'To do', 'in_progress' => 'In progress', 'done' => 'Done'] as $key => $label): ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted"><?= $label ?></small>
                    <div class="display-6"><?= (int) ($counts[$key] ?? 0) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<a class="btn btn-outline-dark" href="board.php">Obrir tauler Kanban</a>
<br>
<p>Has visitat la pàgina <?= (int) $indexVisits ?> vegades.</p>
<div>
    <p>Últimes tasques visitades: <br></p>
    <p> 
        <?php foreach ($_SESSION['tasks'] as $task): ?>
            <?= $task; ?><br>
        <?php endforeach; ?>
    </p>
</div>



<?php require __DIR__ . '/../includes/footer.php'; ?>
