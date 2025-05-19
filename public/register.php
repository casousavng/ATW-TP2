<?php
require_once '../includes/db.php';
require_once '../includes/mailer.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $birth_date = $_POST['birth_date'] ?? '';
    $nationality = $_POST['nationality'] ?? '';
    $country = $_POST['country'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validação simples
    if (!$name || !$birth_date || !$email || !$password) {
        $errors[] = "Todos os campos obrigatórios devem ser preenchidos.";
    }
    if ($password !== $confirm) {
        $errors[] = "As passwords não coincidem.";
    }

    // Verifica se o email já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "Este email já está registado.";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        $stmt = $pdo->prepare("INSERT INTO users (name, birth_date, nationality, country, email, phone, password, verification_token) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $birth_date, $nationality, $country, $email, $phone, $hash, $token]);

        if (sendVerificationEmail($email, $name, $token)) {
            header("Location: register_success.php");
            exit;
        } else {
            $errors[] = "Erro ao enviar email de verificação.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Registo</title>
    <!-- Incluindo o Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Registo de Utilizador</h1>
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Nome*</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="birth_date" class="form-label">Data de Nascimento*</label>
            <input type="date" name="birth_date" id="birth_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email*</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password*</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="confirm" class="form-label">Confirmar Password*</label>
            <input type="password" name="confirm" id="confirm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registar</button>
    </form>
</div>
<!-- Incluindo o JS do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>