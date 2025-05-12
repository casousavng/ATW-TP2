<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

checkAdmin(); // já valida sessão e privilégios

// Buscar os artigos com o nome do autor
$stmt = $pdo->query("
    SELECT a.*, u.name AS username 
    FROM articles a 
    LEFT JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC
");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestão de Artigos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
    <h1>Gestão de Artigos</h1>
    

    <?php if (empty($articles)): ?>
        <div class="alert alert-info">Nenhum artigo encontrado.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Data</th>
                    <th>Visível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $art): ?>
                    <tr>
                        <td><?= $art['id'] ?></td>
                        <td><?= htmlspecialchars($art['title']) ?></td>
                        <td><?= htmlspecialchars($art['username'] ?? 'Desconhecido') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($art['created_at'])) ?></td>
                        <td><?= $art['is_visible'] ? 'Sim' : 'Não' ?></td>
                        <td>
                            <?php if ($art['is_visible']): ?>
                                <a href="ocultar_artigo.php?id=<?= $art['id'] ?>&action=hide" class="btn btn-sm btn-secondary">Ocultar</a>
                            <?php else: ?>
                                <a href="ocultar_artigo.php?id=<?= $art['id'] ?>&action=show" class="btn btn-sm btn-success">Mostrar</a>
                            <?php endif; ?>
                            <a href="apagar_artigo.php?id=<?= $art['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem a certeza que quer apagar este artigo?')">Apagar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>