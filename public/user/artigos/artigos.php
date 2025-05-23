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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página de artigos do utilizador">
    <meta name="keywords" content="artigos, utilizador, comunidade desportiva">
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães">
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon">
    <title>Meus Artigos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style_header.css">
</head>
<body>
<div class="container mt-4">
    
    <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
    <h1 class="mb-4">Meus Artigos</h1>

    <a href="novo_artigo.php" class="btn btn-success mb-4">+ Novo Artigo</a>

    <?php if (empty($artigos)): ?>
        <p class="text-muted">Ainda não escreveste nenhum artigo.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($artigos as $a): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <?php if ($a['image']): ?>
                            <img src="../../<?= htmlspecialchars($a['image']) ?>" class="card-img-top" style="max-height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($a['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($a['content'], 0, 150)) ?>...</p>
                            <a href="editar_artigo.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        </div>
                        <div class="card-footer text-muted">
                            <?= date('d/m/Y H:i', strtotime($a['created_at'])) ?>
                            <?= !$a['is_visible'] ? '<span class="badge bg-warning text-dark">Oculto pelo administrador</span>' : '' ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>