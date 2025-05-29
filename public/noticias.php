<?php
session_start();

// public/noticias.php (CONTROLLER)
require_once '../includes/db.php';

// Verificar se há um parâmetro de pesquisa
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

if ($searchQuery) {
    $stmt = $pdo->prepare("
        SELECT id, titulo, imagem, texto, data_criacao
        FROM noticias
        WHERE visivel = 1
        AND (titulo LIKE ? OR texto LIKE ?)
        ORDER BY data_criacao DESC
    ");
    $stmt->execute(['%' . $searchQuery . '%', '%' . $searchQuery . '%']);
} else {
    $stmt = $pdo->query("SELECT id, titulo, imagem, texto, data_criacao FROM noticias WHERE visivel = 1 ORDER BY data_criacao DESC");
}

$noticias = $stmt->fetchAll();

function getExcerptWithMore($content, $id, $maxSentences = 1) {
    $text = strip_tags($content);
    $sentences = preg_split('/(?<=[.?!])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $excerpt = implode(' ', array_slice($sentences, 0, $maxSentences));

    return htmlspecialchars($excerpt) . ' (Clica para ler mais) ';
}

// No final do controller, inclua a view.
// As variáveis $searchQuery e $noticias estarão disponíveis na view.
include '../includes/header.php'; // Se tiver um header comum
include '../views/public/noticias.php'; // A view específica da listagem de notícias
include '../includes/footer.php'; // Se tiver um footer comum