<?php
require_once '../includes/db.php';
include('../includes/header.php');

$token = $_GET['token'] ?? '';
$feedback = '';
$type = 'danger';

if ($token) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        $feedback = "Conta verificada com sucesso. <a href='../public/login.php' class='alert-link'>Clique aqui para entrar</a>.";
        $type = 'success';
    } else {
        $feedback = "Token inválido ou expirado.";
    }
} else {
    $feedback = "Token em falta.";
}
?>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-<?= $type ?> text-center">
                <?= $feedback ?>
            </div>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>