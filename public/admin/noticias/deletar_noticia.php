<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Buscar a notícia no banco de dados
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $noticia = $stmt->fetch();

    if ($noticia) {
        // Apagar a imagem do diretório
        unlink(__DIR__ . '/../public/uploads/noticias/' . $noticia['imagem']);
        
        // Apagar a notícia do banco de dados
        $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: noticias.php");
        exit;
    } else {
        echo "Notícia não encontrada!";
    }
}
?>