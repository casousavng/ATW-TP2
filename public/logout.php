<?php
session_start();
require_once '../includes/db.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Registar atividade de logout
    $stmt = $pdo->prepare("INSERT INTO atividades (user_id, descricao, tipo_atividade) VALUES (?, ?, ?)");
    $stmt->execute([$userId, 'Logout efetuado', 'logout']);
}

session_destroy();

header("Location: index.php");
exit;