<?php

session_start();

// Recuperar Senha
require_once '../includes/db.php';
require_once '../includes/mailer.php'; 

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if ($email) {
        // Verificar se o email existe no banco
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(16));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expira]);

            // Enviar email de recuperação
            if (sendPasswordResetEmail($email, $user['name'], $token)) {
                $mensagem = "Enviamos um email com o link para redefinir a tua senha.";
            } else {
                $erro = "Erro ao enviar email. Por favor, tenta novamente mais tarde.";
            }
        } else {
            $erro = "Se o teu email constar na nossa base de dados, irás receber um email com instruções.";
        }
    } else {
        $erro = "Por favor, informa um email válido.";
    }
}

// No final do controller, inclui a view.
include '../includes/header.php';
include '../views/auth/recuperar.php';
include '../includes/footer.php';
?>