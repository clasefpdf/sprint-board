<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/functions.php';
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $user = null;
    foreach (loadData()['users'] ?? [] as $candidate) {
        if ($candidate['email'] === $email) { $user = $candidate; break; }
    }
    if ($user && password_verify((string) ($_POST['password'] ?? ''), $user['password'])) {
        session_regenerate_id(true); $_SESSION['user_id'] = $user['id']; 
        redirect('index.php');
    }
    $error = 'El correu o la contrasenya no són correctes.';
}
$pageTitle = 'Inici de sessió';
require __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3 mb-4">Inicia sessió</h1>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">
                        Compte creat correctament. Ja pots iniciar sessió.
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= h($error) ?></div>
                <?php endif; ?>

                <form method="post">
                    <label class="form-label">Correu</label>
                    <input class="form-control mb-3" type="email" name="email" required>

                    <label class="form-label">Contrasenya</label>
                    <input class="form-control mb-3" type="password" name="password" required>

                    <button class="btn btn-primary w-100">Entrar</button>
                </form>

                <p class="small text-muted mt-3 mb-0">
                    Prova: alex@example.test / password
                </p>
                <p class="mt-3 mb-0">
                    <a href="register.php">Crear un compte nou</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
