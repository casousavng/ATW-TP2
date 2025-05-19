<?php
session_start();
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
    $expires = date('Y-m-d H:i:s', time() + 300); // 5 min

    // Atualiza novo código e validade
    $stmt = $pdo->prepare("UPDATE users SET login_token = ?, login_token_expires = ? WHERE id = ?");
    $stmt->execute([$code, $expires, $user['id']]);

    // Envia o código
    sendVerificationCode($user['email'], $user['name'], $code);
}

// Redireciona de volta ao 2FA
header("Location: 2fa.php");
exit;
?>