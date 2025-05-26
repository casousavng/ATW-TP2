<?php
// public/noticia.php (CONTROLLER)

require_once '../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Notícia inválida.");
}

$noticiaId = (int)$_GET['id'];

// Buscar notícia específica que está visível
$stmt = $pdo->prepare("
    SELECT id, titulo, texto, imagem, data_criacao
    FROM noticias
    WHERE id = ? AND visivel = 1
");
$stmt->execute([$noticiaId]);
$noticia = $stmt->fetch();

if (!$noticia) {
    die("Notícia não encontrada.");
}

// Incluir header, view e footer
include '../includes/header.php';
include '../views/public/noticia.php';
include '../includes/footer.php';