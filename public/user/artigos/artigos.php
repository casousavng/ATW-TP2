<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute(['uid' => $user_id]);
$artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Página de artigos do utilizador" />
    <meta name="keywords" content="artigos, utilizador, comunidade desportiva" />
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães" />
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/styles.css" />
    <title>Meus Artigos</title>
</head>
<body>
<div class="container mt-4">

    <a href="../index.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <h1 class="mb-4">Meus Artigos</h1>

    <a href="novo_artigo.php" class="btn btn-success mb-4">
        <i class="bi bi-plus-lg"></i> Novo Artigo
    </a>

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
</div>

<!-- Bootstrap JS Bundle (inclui Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>