<?php
define('BASE_PATH', dirname(__DIR__, 3));  // Caminho base ajustado
require_once(BASE_PATH . '/includes/db.php');  // DB
require_once(BASE_PATH . '/includes/auth.php');  // Auth
checkAdmin();  // Verifica se o usuário é admin

$id = $_GET['id'] ?? null;

if (!$id) {
    die('ID da notícia não fornecido.');
}

// Buscar a notícia
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    die('Notícia não encontrada.');
}

// Se for post, apagar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($noticia['imagem']) {
        $imagemPath = BASE_PATH . '/public/uploads/noticias/' . $noticia['imagem'];
        if (file_exists($imagemPath)) {
            unlink($imagemPath); // Deleta imagem
        }
    }

    $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: add_noticia.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Exclusão de Notícia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <a href="add_noticia.php" class="btn btn-outline-secondary mb-4">← Voltar</a>

    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Confirmar Exclusão</h4>
        </div>
        <div class="card-body">
            <p class="mb-3">Tens a certeza de que desejas excluir esta notícia?</p>

            <h5><?= htmlspecialchars($noticia['titulo']) ?></h5>
            <p><?= nl2br(htmlspecialchars($noticia['texto'])) ?></p>

            <?php if ($noticia['imagem']): ?>
                <img src="/public/uploads/noticias/<?= htmlspecialchars($noticia['imagem']) ?>" alt="Imagem da notícia" class="img-fluid rounded mb-3" style="max-height: 300px;">
            <?php endif; ?>

            <form method="post">
                <button type="submit" class="btn btn-danger">Sim, excluir</button>
                <a href="add_noticia.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>