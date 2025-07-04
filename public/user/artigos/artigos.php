<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0;

$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $imagePath = null;

    if (empty($title)) {
        $errors[] = "O título é obrigatório.";
    }

    if (empty($content)) {
        $errors[] = "O conteúdo é obrigatório.";
    }

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../../uploads/artigos/';
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowedExts)) {
            $errors[] = "Formato de imagem inválido.";
        } else {
            $filename = uniqid('img_') . '.' . $ext;
            $destination = $uploadDir . $filename;

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = '/uploads/artigos/' . $filename;
            } else {
                $errors[] = "Erro ao enviar imagem.";
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO articles (title, image, content, user_id, created_at, is_visible) 
                               VALUES (:title, :image, :content, :uid, NOW(), 1)");
        $stmt->execute([
            'title' => $title,
            'image' => $imagePath,
            'content' => $content,
            'uid' => $user_id
        ]);

        $stmt = $pdo->prepare("INSERT INTO atividades (user_id, descricao, tipo_atividade) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, "Artigo '$title' publicado", 'outro']);

        header('Location: artigos.php');
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute(['uid' => $user_id]);
$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Meus Artigos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../../assets/css/styles.css" />
</head>
<body>
<div class="container mt-4">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <br>

    <button id="toggleFormBtn" class="btn btn-success mb-4">
        <i class="bi bi-plus-lg me-1"></i> Novo Artigo
    </button>

    <!-- Formulário Novo Artigo -->
    <div id="form-artigo" class="mb-3 d-none">
        <h3>Escrever Novo Artigo</h3>

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
            <button type="button" class="btn btn-secondary ms-2" onclick="toggleForm()">
                <i class="bi bi-x-lg"></i> Cancelar
            </button>
        </form>
        <hr>
    </div>

    <h1 class="mb-4">Meus Artigos</h1>

    <!-- Lista de Artigos -->
    <?php if (empty($artigos)): ?>
        <p class="text-muted">Ainda não escreveste nenhum artigo.</p>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php foreach ($artigos as $a): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php if ($a['image']): ?>
                            <img src="../../<?= htmlspecialchars($a['image']) ?>"
                                 class="card-img-top"
                                 style="max-height: 200px; object-fit: cover;"
                                 alt="Imagem do artigo <?= htmlspecialchars($a['title']) ?>">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($a['title']) ?></h5>
                            <p class="card-text flex-grow-1"><?= htmlspecialchars(substr($a['content'], 0, 150)) ?>...</p>
                            <a href="editar_artigo.php?id=<?= $a['id'] ?>" class="btn btn-primary btn-sm mt-auto">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </div>
                        <div class="card-footer text-muted d-flex justify-content-between align-items-center">
                            <small><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></small>
                            <?php if (!$a['is_visible']): ?>
                                <span class="badge bg-warning text-dark">Oculto pelo administrador</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <br>

    <button id="backToTopBtn" title="Voltar ao topo">
        <i class="bi bi-arrow-up"></i>
    </button>
</div>


<script src="../../../assets/script/script.js"></script>

<script>
    const formSection = document.getElementById('form-artigo');
    const toggleBtn = document.getElementById('toggleFormBtn');

    function toggleForm() {
        formSection.classList.toggle('d-none');

        if (!formSection.classList.contains('d-none')) {
            toggleBtn.classList.remove('btn-success');
            toggleBtn.classList.add('btn-danger');
            toggleBtn.innerHTML = '<i class="bi bi-x-lg me-1"></i> Fechar Formulário';
        } else {
            toggleBtn.classList.remove('btn-danger');
            toggleBtn.classList.add('btn-success');
            toggleBtn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Novo Artigo';
        }
    }

    toggleBtn.addEventListener('click', toggleForm);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>