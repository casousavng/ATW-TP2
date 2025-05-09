<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

checkLogin(); // Garante que só utilizadores autenticados acedem

$userId = $_SESSION['user_id'];

// Buscar os dados do utilizador
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Área do Utilizador</title>
    <!-- Incluindo o Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Olá, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>

    <!-- Link para Logout -->
    <a href="../logout.php" class="btn btn-danger mb-4">Terminar Sessão</a>

    <h2>Os teus dados:</h2>
    <ul class="list-group">
        <li class="list-group-item"><strong>Nome:</strong> <?= htmlspecialchars($user['name']) ?></li>
        <li class="list-group-item"><strong>Data de nascimento:</strong> <?= htmlspecialchars($user['birth_date']) ?></li>
        <li class="list-group-item"><strong>Nacionalidade:</strong> <?= htmlspecialchars($user['nationality']) ?></li>
        <li class="list-group-item"><strong>País de residência:</strong> <?= htmlspecialchars($user['country']) ?></li>
        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
        <li class="list-group-item"><strong>Telefone:</strong> <?= htmlspecialchars($user['phone']) ?></li>
    </ul>

    <p class="mt-4"><a href="edit.php" class="btn btn-primary">Editar perfil</a></p>
</div>

<!-- Incluindo o JS do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>