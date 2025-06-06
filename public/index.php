<?php
session_start();
// public/index.php (CONTROLLER)

require_once '../includes/db.php';

// Imagens em destaque
$stmt = $pdo->query("SELECT caminho FROM imagem_destaque ORDER BY atualizado_em DESC");
$imagensDestaque = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Últimas 3 notícias (adicionando id para links)
$stmtNoticias = $pdo->query("SELECT id, titulo, imagem, texto FROM noticias WHERE visivel = 1 ORDER BY data_criacao DESC LIMIT 3");
$noticias = $stmtNoticias->fetchAll(PDO::FETCH_ASSOC);

// Últimos 3 artigos com contagem de comentários
$stmtArtigos = $pdo->query("
    SELECT a.id, a.title, a.image, a.content, 
           (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id AND c.is_verified = 1) AS comentarios_count
    FROM articles a 
    WHERE a.is_visible = 1 
    ORDER BY a.created_at DESC 
    LIMIT 3
");
$artigos = $stmtArtigos->fetchAll(PDO::FETCH_ASSOC);

function getExcerpt(string $text): string {
    // Extrai a primeira frase (até o primeiro ponto)
    if (preg_match('/^(.*?\.)(\s|$)/u', $text, $matches)) {
        return trim($matches[1]);
    }
    return trim($text);
}

// Incluir header, view e footer
include '../includes/header.php';
include '../views/public/index.php';
include '../includes/footer.php';

?>

