<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/mailer.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Artigo inválido.");
}

$articleId = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT a.*, u.name AS author 
    FROM articles a 
    JOIN users u ON a.user_id = u.id 
    WHERE a.id = ? AND a.is_visible = 1
");
$stmt->execute([$articleId]);
$article = $stmt->fetch();

if (!$article) {
    die("Artigo não encontrado.");
}

$errors = [];
$name = '';
$email = '';
$comment = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if (empty($name)) $errors[] = "O nome é obrigatório.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if (empty($comment)) {
        $errors[] = "O comentário é obrigatório.";
    } elseif (mb_strlen($comment) > 100) {
        $errors[] = "O comentário não pode ter mais de 100 caracteres.";
    }

    if (empty($errors)) {
        $token = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("
            INSERT INTO comments (article_id, name, email, comment, token, is_verified) 
            VALUES (?, ?, ?, ?, ?, 0)
        ");
        $stmt->execute([$articleId, $name, $email, $comment, $token]);

        sendCommentVerificationEmail(
            $email,
            $name,
            $token
        );

        header("Location: artigo.php?id=$articleId&pending=1");
        exit;
    }
}

$stmt = $pdo->prepare("
    SELECT * FROM comments 
    WHERE article_id = ? AND is_verified = 1 
    ORDER BY created_at DESC
");
$stmt->execute([$articleId]);
$comments = $stmt->fetchAll();

$voltar_para = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'artigos.php';

include '../includes/header.php';
include '../views/public/artigo.php';
include '../includes/footer.php';
?>