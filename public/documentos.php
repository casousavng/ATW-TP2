<?php

session_start();
// public/documentos.php (CONTROLLER)

require_once("../includes/db.php"); // Liga à base de dados

// Verificar se há um parâmetro de pesquisa
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

// Ajustar a consulta SQL para filtrar os documentos com base na pesquisa
if ($searchQuery) {
    $stmt = $pdo->prepare("
        SELECT nome_personalizado, nome_ficheiro
        FROM documentos
        WHERE nome_personalizado LIKE ? OR nome_ficheiro LIKE ?
        ORDER BY data_upload DESC
    ");
    $stmt->execute(['%' . $searchQuery . '%', '%' . $searchQuery . '%']);
} else {
    // Caso não haja pesquisa, exibe todos os documentos
    $stmt = $pdo->query("SELECT nome_personalizado, nome_ficheiro FROM documentos ORDER BY data_upload DESC");
}

$documentos = $stmt->fetchAll();

// No final do controller, inclua a view.
// As variáveis $searchQuery e $documentos estarão disponíveis na view.
include "../includes/header.php"; // Se tiver um header comum
include "../views/public/documentos.php"; // A view específica da listagem de documentos
include "../includes/footer.php"; // Se tiver um footer comum