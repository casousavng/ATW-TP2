<?php
// public/artigo.php (CONTROLLER)

require_once("../includes/db.php"); // Liga à base de dados

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Artigo inválido.");
}

$articleId = (int)$_GET['id'];

// Buscar artigo
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

// Processar envio do comentário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if (empty($name)) {
        $errors[] = "O nome é obrigatório.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido.";
    }

    if (empty($comment)) {
        $errors[] = "O comentário é obrigatório.";
    } elseif (mb_strlen($comment) > 100) {
        $errors[] = "O comentário não pode ter mais de 100 caracteres.";
    }

    if (empty($errors)) {
        // Inserir comentário (email não é salvo, só nome e comentário)
        $stmt = $pdo->prepare("INSERT INTO comments (article_id, name, comment) VALUES (?, ?, ?)");
        $stmt->execute([$articleId, $name, $comment]);

        // Redirecionar para evitar reenvio do formulário
        header("Location: artigo.php?id=$articleId");
        exit;
    }
}

// Buscar comentários do artigo
$stmt = $pdo->prepare("SELECT * FROM comments WHERE article_id = ? ORDER BY created_at DESC");
$stmt->execute([$articleId]);
$comments = $stmt->fetchAll();

// Chama a view
include '../includes/header.php';
include '../views/public/artigo.php';
include '../includes/footer.php';