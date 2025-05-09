<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

$errors = [];
$success = false;

$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$password || !$confirmPassword) {
        $errors[] = "Todos os campos são obrigatórios.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "As senhas não coincidem.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $errors[] = "O email já está em uso.";
        } else {
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashPassword, 1]);
            $success = true;
            $name = $email = '';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Criar Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white py-4">
        <div class="container text-center">
            <h1 class="mb-0">👤 Criar Novo Administrador</h1>
        </div>
    </header>

    <div class="container my-5" style="max-width: 600px;">
        <a href="dashboard.php" class="btn btn-outline-secondary mb-4">← Voltar ao Painel</a>

        <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                Administrador criado com sucesso!
            </div>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endforeach; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control <?= in_array("Todos os campos são obrigatórios.", $errors) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control <?= in_array("Todos os campos são obrigatórios.", $errors) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control <?= in_array("Todos os campos são obrigatórios.", $errors) ? 'is-invalid' : '' ?>" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar Senha</label>
                <input type="password" class="form-control <?= in_array("As senhas não coincidem.", $errors) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Criar Administrador</button>
        </form>
    </div>

    <footer class="bg-dark text-white py-3 mt-5">
        <div class="container text-center">
            <small>&copy; <?= date('Y') ?> Comunidade Desportiva</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>