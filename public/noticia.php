<?php
session_start();

// public/noticia.php (CONTROLLER)
require_once '../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Notícia inválida.");
}

$noticia_ja_guardada = false;

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

$voltar_para = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'noticias.php';

// Tratamento de notícias guardadas

if (!$noticia) {
    echo "Notícia não encontrada.";
    exit;
}

// Verifica se o utilizador está autenticado
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : null;

// Guardar o conteúdo se for pedido
if ($isLoggedIn && isset($_POST['guardar_noticia'])) {
    if (!$noticia_ja_guardada) {
        $stmt = $pdo->prepare("
            INSERT INTO conteudos_guardados (user_id, conteudo_id, tipo_conteudo)
            VALUES (:user_id, :conteudo_id, 'noticia')
        ");
        $stmt->execute([
            'user_id' => $user_id,
            'conteudo_id' => $noticiaId
        ]);
        $guardada_sucesso = true;
        $noticia_ja_guardada = true;
    } else {
        $ja_guardada = true;
    }
}

$noticia_ja_guardada = false;

if ($isLoggedIn) {
    $check = $pdo->prepare("
        SELECT 1 FROM conteudos_guardados 
        WHERE user_id = :user_id AND conteudo_id = :conteudo_id AND tipo_conteudo = 'noticia'
    ");
    $check->execute([
        'user_id' => $user_id,
        'conteudo_id' => $noticiaId
    ]);
    $noticia_ja_guardada = $check->fetchColumn() ? true : false;
}

$user_id = $_SESSION['user_id'] ?? null;
$noticia_id = intval($_GET['id'] ?? 0);

if ($user_id && $noticia_id) {
    $sql = "UPDATE conteudos_guardados 
            SET vezes_consultado = vezes_consultado + 1 
            WHERE user_id = :uid 
              AND tipo_conteudo = 'noticia' 
              AND conteudo_id = :cid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'uid' => $user_id,
        'cid' => $noticia_id
    ]);
}

// Incluir header, view e footer
include '../includes/header.php';
include '../views/public/noticia.php';
include '../includes/footer.php';