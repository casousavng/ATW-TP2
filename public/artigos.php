<?php
// public/artigos.php (CONTROLLER)

require_once '../includes/db.php';

// Verificar se há um parâmetro de pesquisa
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

// Ajustar a consulta SQL para filtrar os artigos com base na pesquisa
if ($searchQuery) {
    // A consulta abaixo vai procurar o termo de pesquisa em qualquer parte do título ou conteúdo
    $stmt = $pdo->prepare("
        SELECT a.id, a.title, a.image, a.content, a.created_at, u.name AS author
        FROM articles a
        JOIN users u ON a.user_id = u.id
        WHERE a.is_visible = 1
        AND (a.title LIKE ? OR a.content LIKE ?)
        ORDER BY a.created_at DESC
    ");
    // O '%' antes e depois da pesquisa é necessário para procurar em qualquer parte do texto
    $stmt->execute(['%' . $searchQuery . '%', '%' . $searchQuery . '%']);
} else {
    // Caso não haja pesquisa, exibe todos os artigos
    $stmt = $pdo->prepare("
        SELECT a.id, a.title, a.image, a.content, a.created_at, u.name AS author
        FROM articles a
        JOIN users u ON a.user_id = u.id
        WHERE a.is_visible = 1
        ORDER BY a.created_at DESC
    ");
    $stmt->execute();
}

$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// No final do controller, inclua a view.
// As variáveis $artigos e $searchQuery estarão disponíveis na view.
include '../includes/header.php'; // Se tiver um header comum
include '../views/public/artigos.php'; // A view específica da listagem de artigos
include '../includes/footer.php'; // Se tiver um footer comum