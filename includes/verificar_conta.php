<?php
require_once '../includes/db.php';
include('../includes/header.php');

$token = $_GET['token'] ?? '';
$title = '';
$message = '';
$alertType = 'danger';
$link = '';

if ($token) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        $title = "Conta Verificada com Sucesso!";
        $message = "A tua conta foi ativada. <a href='../public/login.php' class='btn btn-link p-0'>Clique aqui para entrar</a>";
        $alertType = 'success';
        $iconClass = 'bi-check-circle-fill text-success';
    } else {
        $title = "Token Inválido";
        $message = "O token fornecido é inválido ou expirou.";
        $alertType = 'danger';
        $iconClass = 'bi-x-circle-fill text-danger';
    }
} else {
    $title = "Token em Falta";
    $message = "Nenhum token foi fornecido.";
    $alertType = 'warning';
    $iconClass = 'bi-exclamation-triangle-fill text-warning';
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <title>Verificação de Conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex flex-column min-vh-100">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow rounded-4 border-<?= $alertType ?>">
                <div class="card-body text-center p-5">
                    <i class="bi <?= $iconClass ?> display-4 mb-3"></i>
                    <h2 class="card-title mb-4"><?= $title ?></h2>
                    <p class="card-text fs-5"><?= $message ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>