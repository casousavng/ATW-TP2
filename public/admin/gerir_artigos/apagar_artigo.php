<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Primeiro apagar imagem se existir
    $stmt = $pdo->prepare("SELECT image FROM articles WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    if ($article && $article['image'] && file_exists('../' . $article['image'])) {
        unlink('../' . $article['image']);
    }

    // Depois apagar artigo
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

// Verificar a página de onde veio e redirecionar corretamente
$redirectUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'artigos.php';

header('Location: ' . $redirectUrl);
exit;