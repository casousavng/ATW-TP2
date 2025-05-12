<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once BASE_PATH . '/includes/db.php';
require_once BASE_PATH . '/includes/auth.php';
checkAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: add_noticia.php');
    exit;
}

$id = (int) $_GET['id'];

// Verificar estado atual
$stmt = $pdo->prepare("SELECT visivel FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if ($noticia) {
    $novoEstado = $noticia['visivel'] ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE noticias SET visivel = ? WHERE id = ?");
    $stmt->execute([$novoEstado, $id]);
}

header('Location: add_noticia.php');
exit;