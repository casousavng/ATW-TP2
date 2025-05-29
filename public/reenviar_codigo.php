<?php
session_start();

// public/reenviar_codigo.php (CONTROLLER)
require_once '../includes/db.php';
require_once '../includes/mailer.php';

if (!isset($_SESSION['temp_user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['temp_user_id'];

// Vai buscar o utilizador
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $code = random_int(100000, 999999);
    $code_hash = hash('sha256', $code);
    $expires = date('Y-m-d H:i:s', time() + 300); // 5 min

    // Atualiza novo código (hashed) e validade
    $stmt = $pdo->prepare("UPDATE users SET login_token = ?, login_token_expires = ? WHERE id = ?");
    $stmt->execute([$code_hash, $expires, $user['id']]);

    // Envia o código original (não o hash)
    sendVerificationCode($user['email'], $user['name'], $code);
}

// Redireciona de volta ao 2FA
header("Location: 2fa.php");
exit;
?>