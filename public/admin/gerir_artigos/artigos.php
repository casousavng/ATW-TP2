<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

checkAdmin(); // já valida sessão e privilégios

// Apagar artigo via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Apagar imagem associada (se houver)
    $stmt = $pdo->prepare("SELECT image FROM articles WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    if ($article && $article['image'] && file_exists('../' . $article['image'])) {
        unlink('../' . $article['image']);
    }

    // Apagar artigo
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
    $stmt->execute(['id' => $id]);

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Buscar os artigos
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Gestão de Artigos, Comunidade Desportiva">
    <meta name="author" content="Comunidade Desportiva">
    <meta name="description" content="Gestão de Artigos">
    <title>Admin - Gestão de Artigos</title>
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media (max-width: 768px) {
            .table-view { display: none; }
        }

        @media (min-width: 769px) {
            .card-view { display: none; }
        }

        .fixed-btn {
            min-width: 90px;
        }

    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
    <h1>Gestão de Artigos</h1>

    <?php if (empty($articles)): ?>
        <div class="alert alert-info">Nenhum artigo encontrado.</div>
    <?php else: ?>

        <!-- TABELA PARA TELAS GRANDES -->
        <div class="table-view">
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
                                <a href="ocultar_artigo.php?id=<?= $art['id'] ?>&action=hide" class="btn btn-sm btn-secondary fixed-btn" title="Ocultar">
                                    <i class="bi bi-eye-slash"></i> Ocultar
                                </a>
                            <?php else: ?>
                                <a href="ocultar_artigo.php?id=<?= $art['id'] ?>&action=show" class="btn btn-sm btn-success fixed-btn" title="Mostrar">
                                    <i class="bi bi-eye"></i> Mostrar
                                </a>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-danger fixed-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $art['id'] ?>" title="Apagar">
                                <i class="bi bi-trash"></i> Apagar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- CARDS PARA MOBILE -->
        <div class="card-view">
            <?php foreach ($articles as $art): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($art['title']) ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            Por <?= htmlspecialchars($art['username'] ?? 'Desconhecido') ?> em <?= date('d/m/Y H:i', strtotime($art['created_at'])) ?>
                        </h6>
                        <p class="card-text"><strong>Visível:</strong> <?= $art['is_visible'] ? 'Sim' : 'Não' ?></p>
                        <div class="d-flex gap-2">
                            <?php if ($art['is_visible']): ?>
                                <a href="ocultar_artigo.php?id=<?= $art['id'] ?>&action=hide" class="btn btn-sm btn-secondary fixed-btn">Ocultar</a>
                            <?php else: ?>
                                <a href="ocultar_artigo.php?id=<?= $art['id'] ?>&action=show" class="btn btn-sm btn-success fixed-btn">Mostrar</a>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-danger fixed-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $art['id'] ?>">Apagar</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja apagar este artigo? Esta ação não poderá ser desfeita.
                <input type="hidden" name="delete_id" id="delete-article-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Apagar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Preenche o ID do artigo no modal
const deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const articleId = button.getAttribute('data-id');
    document.getElementById('delete-article-id').value = articleId;
});
</script>
</body>
</html>