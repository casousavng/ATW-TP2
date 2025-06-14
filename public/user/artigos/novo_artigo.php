<?php
define('BASE_PATH', dirname(__DIR__, 3)); // Sobe 3 níveis
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
                // Caminho relativo para BD e web
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

        // Registrar atividade
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Comunidade Desportiva - Novo Artigo" />
    <meta name="keywords" content="Comunidade Desportiva, Novo Artigo" />
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães" />
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <link rel="stylesheet" href="../assets/css/styles.css" />

    <style>
        textarea { resize: none; }
    </style>

    <title>Novo Artigo</title>
</head>
<body>
<div class="container mt-4">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
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

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required />
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Imagem (opcional)</label>
            <input type="file" name="image" id="image" class="form-control" accept=".jpg,.jpeg,.png,.gif" />
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Conteúdo</label>
            <textarea name="content" id="content" class="form-control" rows="6" required><?= htmlspecialchars($content) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-upload"></i> Publicar
        </button>
        <a href="artigos.php" class="btn btn-secondary ms-2">
            <i class="bi bi-x-lg"></i> Cancelar
        </a>
    </form>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>