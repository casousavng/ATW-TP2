<?php
define('BASE_PATH', dirname(__DIR__, 3)); // Sobe 2 níveis (de user/artigos para raiz do projeto)
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $imagePath = null;

    if (empty($title)) {
        $errors[] = "O título é obrigatório.";
    }

    if (empty($content)) {
        $errors[] = "O conteúdo é obrigatório.";
    }

    // Lidar com imagem
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../../uploads/artigos/';
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowedExts)) {
            $errors[] = "Formato de imagem inválido.";
        } else {
            $filename = uniqid('img_') . '.' . $ext;
            $destination = $uploadDir . $filename;

            // Cria o diretório se não existir
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                // Caminho relativo para guardar na BD e usar na web
                $imagePath = '/uploads/artigos/' . $filename;
            } else {
                $errors[] = "Erro ao enviar imagem.";
            }
        }
    }

    // Inserir no banco de dados
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO articles (title, image, content, user_id, created_at, is_visible) 
                               VALUES (:title, :image, :content, :uid, NOW(), 1)");
        $stmt->execute([
            'title' => $title,
            'image' => $imagePath,
            'content' => $content,
            'uid' => $user_id
        ]);

        // Registrar a atividade de adicionar um novo artigo
        $stmt = $pdo->prepare("INSERT INTO atividades (user_id, descricao, tipo_atividade) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, "Artigo '$title' publicado", 'outro']);

        header('Location: artigos.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Novo Artigo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Escrever Novo Artigo</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($title) ?>">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Imagem (opcional)</label>
            <input type="file" name="image" id="image" class="form-control">
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Conteúdo</label>
            <textarea name="content" id="content" class="form-control" rows="6"><?= htmlspecialchars($content) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Publicar</button>
        <a href="artigos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>