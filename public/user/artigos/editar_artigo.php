<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

$isAdmin = $_SESSION['isAdmin'] ?? 0;
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Artigo inválido.");
}

$articleId = $_GET['id'];

// Verificar se o artigo pertence ao utilizador (ou se é admin)
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
$stmt->execute(['id' => $articleId]);
$article = $stmt->fetch();

if (!$article || (!$isAdmin && $article['user_id'] != $userId)) {
    die("Sem permissão para editar este artigo.");
}

$errors = [];
$title = $article['title'];
$content = $article['content'];
$imagePath = $article['image'];

// Processar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title)) {
        $errors[] = "O título é obrigatório.";
    }

    if (empty($content)) {
        $errors[] = "O conteúdo é obrigatório.";
    }

    // Atualizar imagem se foi enviada nova
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../../uploads/artigos/';
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowedExts)) {
            $errors[] = "Formato de imagem inválido.";
        } else {
            $filename = uniqid('img_') . '.' . $ext;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = '/uploads/artigos/' . $filename;
            } else {
                $errors[] = "Erro ao enviar nova imagem.";
            }
        }
    }

    if (empty($errors)) {
        // Atualizar o artigo
        $stmt = $pdo->prepare("UPDATE articles SET title = :title, image = :image, content = :content WHERE id = :id");
        $stmt->execute([
            'title' => $title,
            'image' => $imagePath,
            'content' => $content,
            'id' => $articleId
        ]);

        // Registrar atividade de edição
        $stmt = $pdo->prepare("INSERT INTO atividades (user_id, descricao, tipo_atividade) VALUES (?, ?, ?)");
        $stmt->execute([$userId, "Artigo '$title' editado", 'edição']);

        $_SESSION['flash'] = "Artigo atualizado com sucesso!";
        header('Location: artigos.php');
        exit;
    }
}

// Processar remoção
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
    $stmt->execute(['id' => $articleId]);

    // Registrar atividade de remoção
    $stmt = $pdo->prepare("INSERT INTO atividades (user_id, descricao, tipo_atividade) VALUES (?, ?, ?)");
    $stmt->execute([$userId, "Artigo '$title' apagado", 'outro']);

    $_SESSION['flash'] = "Artigo apagado com sucesso!";
    header('Location: artigos.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Editar Artigo - Área do Utilizador" />
    <meta name="keywords" content="Editar Artigo, Área do Utilizador, Comunidade Desportiva" />
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães" />
    <link rel="icon" href="../../assets/favicon/favicon.jpg" type="image/x-icon" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <link rel="stylesheet" href="../../assets/css/styles.css" />

    <title>Editar Artigo</title>
</head>
<body>
<div class="container mt-4">
    <h1>Editar Artigo</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Imagem Atual</label>
            <?php if ($imagePath): ?>
                <div class="mb-3">
                    <img src="../../<?= htmlspecialchars($imagePath) ?>" alt="Imagem atual" class="img-thumbnail" style="max-height: 200px;">
                </div>
            <?php else: ?>
                <p class="text-muted">Sem imagem.</p>
            <?php endif; ?>
            <label for="image" class="form-label">Nova Imagem (opcional)</label>
            <input type="file" name="image" id="image" class="form-control" accept=".jpg,.jpeg,.png,.gif">
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Conteúdo</label>
            <textarea name="content" id="content" class="form-control" rows="10" required style="resize: none;"><?= htmlspecialchars($content) ?></textarea>
        </div>

        <button type="submit" name="update" class="btn btn-primary">
            <i class="bi bi-save2"></i> Guardar Alterações
        </button>
        <a href="artigos.php" class="btn btn-secondary ms-2">
            <i class="bi bi-x-lg"></i> Cancelar
        </a>
    </form>

    <hr>

    <form id="deleteForm" method="POST">
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
            <i class="bi bi-trash"></i> Apagar Artigo
        </button>
        <input type="hidden" name="delete" value="1" />
    </form>
    <br>
</div>

<!-- Modal de confirmação -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Tens a certeza que queres apagar este artigo? <br><strong>Esta ação não pode ser desfeita.</strong>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Apagar</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle (Popper incluído) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    document.getElementById('deleteForm').submit();
  });
</script>
</body>
</html>