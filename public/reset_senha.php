<?php
session_start();

// public/reset_senha.php (CONTROLLER)
require_once '../includes/db.php';

$token = $_GET['token'] ?? '';
$erro = '';
$mensagem = '';

if (!$token) {
    die("Token inválido.");
}

// Verifica token válido e não expirado
$stmt = $pdo->prepare("SELECT pr.user_id, u.email FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE pr.token = ? AND pr.expires_at > NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    die("Token inválido ou expirado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'] ?? '';
    $senhaConfirma = $_POST['senha_confirm'] ?? '';

    if (strlen($senha) < 6) {
        $erro = "A Senha deve ter no mínimo 6 caracteres.";
    } elseif ($senha !== $senhaConfirma) {
        $erro = "As senhas não coincidem.";
    } else {
        // Atualiza senha (exemplo com password_hash)
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $reset['user_id']]);

        // Remove token após uso
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);

        $mensagem = "Senha alterada com sucesso. Já podes fazer login.";
    }
}

// No final do controller, inclua a view.
// As variáveis $searchQuery e $noticias estarão disponíveis na view.
include '../includes/header.php'; 
include '../views/auth/redefinir_senha.php'; // A view específica da listagem de notícias
include '../includes/footer.php'; 

?>