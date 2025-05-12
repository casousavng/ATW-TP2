<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

define('UPLOAD_DIR', BASE_PATH . '/public/uploads/destaque/');

// Processar uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem'])) {
    foreach ($_FILES['imagem']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['imagem']['error'][$index] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['imagem']['name'][$index], PATHINFO_EXTENSION);
            $nomeFicheiro = 'destaque_' . time() . '_' . $index . '.' . $extensao;
            $destino = UPLOAD_DIR . $nomeFicheiro;

            if (move_uploaded_file($tmpName, $destino)) {
                $pdo->prepare("INSERT INTO imagem_destaque (caminho) VALUES (?)")->execute([$nomeFicheiro]);
                $sucesso = true;
            } else {
                $erro = "Erro ao mover a imagem $index.";
            }
        }
    }
}

// Remover imagem
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT caminho FROM imagem_destaque WHERE id = ?");
    $stmt->execute([$id]);
    $caminho = $stmt->fetchColumn();

    if ($caminho && file_exists(UPLOAD_DIR . $caminho)) {
        unlink(UPLOAD_DIR . $caminho);
    }

    $pdo->prepare("DELETE FROM imagem_destaque WHERE id = ?")->execute([$id]);
    header("Location: editar_imagem_destaque.php");
    exit;
}

$imagens = $pdo->query("SELECT id, caminho, atualizado_em FROM imagem_destaque ORDER BY atualizado_em DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerir Imagens em Destaque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
    <h2>Imagens em Destaque</h2>

    <?php if (!empty($sucesso)): ?>
        <div class="alert alert-success">Imagens carregadas com sucesso!</div>
    <?php elseif (!empty($erro)): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label for="imagem" class="form-label">Escolher imagens (várias):</label>
            <input type="file" name="imagem[]" id="imagem" class="form-control" multiple required>
        </div>
        <button type="submit" class="btn btn-primary">Adicionar Imagens</button>
    </form>

    <div class="row">
        <?php foreach ($imagens as $img): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="/public/uploads/destaque/<?= htmlspecialchars($img['caminho']) ?>" class="card-img-top" style="max-height: 200px; object-fit: cover;">
                    <div class="card-body text-center">
                        <a href="?delete=<?= $img['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover esta imagem?')">Remover</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>