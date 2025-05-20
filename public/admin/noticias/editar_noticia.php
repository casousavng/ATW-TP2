<?php
define('BASE_PATH', dirname(__DIR__, 3));  // Caminho base ajustado
require_once BASE_PATH . '/includes/db.php';  // DB
require_once BASE_PATH . '/includes/auth.php';  // Auth
checkAdmin(); // Garante que o usuário é administrador

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Buscar a notícia do banco de dados
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $noticia = $stmt->fetch();

    if (!$noticia) {
        echo "Notícia não encontrada!";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulo = $_POST['titulo'];
        $texto = $_POST['texto'];

        // Se o administrador quiser mudar a imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagem = $_FILES['imagem'];
            $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
            $nomeFicheiro = 'noticia_' . time() . '.' . $extensao;
            $destino = BASE_PATH . '/public/uploads/noticias/' . $nomeFicheiro;

            if (move_uploaded_file($imagem['tmp_name'], $destino)) {
                // Apagar a imagem antiga, se houver
                unlink(BASE_PATH . '/public/uploads/noticias/' . $noticia['imagem']);
                // Atualizar a notícia com a nova imagem
                $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, imagem = ?, texto = ? WHERE id = ?");
                $stmt->execute([$titulo, $nomeFicheiro, $texto, $id]);
                $sucesso = "Notícia atualizada com sucesso!";
            } else {
                $erro = "Erro ao carregar a imagem.";
            }
        } else {
            // Atualizar sem imagem
            $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, texto = ? WHERE id = ?");
            $stmt->execute([$titulo, $texto, $id]);
            $sucesso = "Notícia atualizada com sucesso!";
        }

        // Redireciona para a página de listagem de notícias após a atualização
        header('Location: add_noticia.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Notícia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <a href="add_noticia.php" class="btn btn-outline-secondary mb-4">← Voltar</a>

    <h2>Editar Notícia</h2>

    <!-- Mensagens de sucesso ou erro -->
    <?php if (!empty($sucesso)): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
    <?php elseif (!empty($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>

    <!-- Formulário para editar a notícia -->
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="<?= htmlspecialchars($noticia['titulo']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem</label>
            <input type="file" name="imagem" id="imagem" class="form-control">
            <img src="/public/uploads/noticias/<?= htmlspecialchars($noticia['imagem']) ?>" class="img-fluid mt-2" style="max-width: 200px;">
        </div>

        <div class="mb-3">
            <label for="texto" class="form-label">Texto</label>
            <textarea name="texto" id="texto" class="form-control" rows="5" required><?= htmlspecialchars($noticia['texto']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Notícia</button>
        <a href="add_noticia.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

</body>
</html>