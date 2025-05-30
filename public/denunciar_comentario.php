<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/mailer.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = (int)($_POST['comment_id'] ?? 0);
    $reporterName = trim($_POST['reporter_name'] ?? '');
    $reporterEmail = trim($_POST['reporter_email'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    if (!$commentId || !$reporterName || !$reporterEmail || !$reason) {
        die("Todos os campos são obrigatórios.");
    }

    if (!filter_var($reporterEmail, FILTER_VALIDATE_EMAIL)) {
        die("Email inválido.");
    }

    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch();

    if (!$comment) {
        die("Comentário não encontrado.");
    }

    // Atualiza o campo 'denunciado' no comentário (incrementa ou define como 1)
    $update = $pdo->prepare("UPDATE comments SET denunciado = denunciado + 1 WHERE id = ?");
    $update->execute([$commentId]);

    // Envio de email para o administrador
    $adminEmail = getenv('ADMIN_EMAIL');
    enviarDenunciaComentario($adminEmail, $comment, $reporterName, $reporterEmail, $reason);

    header("Location: artigo.php?id=" . $comment['article_id'] . "&denuncia=1");
    exit;
} else {
    die("Acesso inválido.");
}