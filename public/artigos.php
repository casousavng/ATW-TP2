<?php
// public/artigos.php (CONTROLLER)

require_once '../includes/db.php';

// Obter o termo de pesquisa (se existir)
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

// Montar a consulta SQL com contagem de comentários
if ($searchQuery) {
    $stmt = $pdo->prepare("
        SELECT 
            a.id, a.title, a.image, a.content, a.created_at, 
            u.name AS author,
            (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) AS comments_count
        FROM articles a
        JOIN users u ON a.user_id = u.id
        WHERE a.is_visible = 1
          AND (a.title LIKE ? OR a.content LIKE ?)
        ORDER BY a.created_at DESC
    ");
    $stmt->execute(['%' . $searchQuery . '%', '%' . $searchQuery . '%']);
} else {
    $stmt = $pdo->prepare("
        SELECT 
            a.id, a.title, a.image, a.content, a.created_at, 
            u.name AS author,
            (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) AS comments_count
        FROM articles a
        JOIN users u ON a.user_id = u.id
        WHERE a.is_visible = 1
        ORDER BY a.created_at DESC
    ");
    $stmt->execute();
}

$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Incluir header, view e footer
include '../includes/header.php';
include '../views/public/artigos.php';
include '../includes/footer.php';