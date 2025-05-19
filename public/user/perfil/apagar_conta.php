<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

checkLogin();

$userId = $_SESSION['user_id'];
$confirm = $_POST['confirmar_apagar'] ?? '';
$userName = $_SESSION['user_name'] ?? '';

if (strtolower(trim($confirm)) !== 'apagar ' . strtolower($userName)) {
    die('Confirmação inválida. A conta não foi apagada.');
}

// Eliminar artigos, dados, valores extra, etc. (adapta conforme a tua base de dados)
$pdo->prepare("DELETE FROM articles WHERE user_id = ?")->execute([$userId]);
$pdo->prepare("DELETE FROM user_extra_values WHERE user_id = ?")->execute([$userId]);
$pdo->prepare("DELETE FROM atividades WHERE user_id = ?")->execute([$userId]);
$pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);

// Terminar sessão
session_destroy();

header('Location: ../index.php?mensagem=Conta apagada com sucesso');
exit;