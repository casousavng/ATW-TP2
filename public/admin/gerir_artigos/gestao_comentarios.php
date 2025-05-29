<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

// Apagar comentário se solicitado
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: gestao_comentarios.php");
    exit;
}

// --- Filtros ---
$where = [];
$params = [];

if (!empty($_GET['email'])) {
    $where[] = 'c.email LIKE :email';
    $params[':email'] = '%' . $_GET['email'] . '%';
}

if (!empty($_GET['article'])) {
    $where[] = 'a.title LIKE :article';
    $params[':article'] = '%' . $_GET['article'] . '%';
}

if (isset($_GET['status']) && $_GET['status'] !== '') {
    $where[] = 'c.is_verified = :status';
    $params[':status'] = $_GET['status'];
}

if (!empty($_GET['date'])) {
    $where[] = 'DATE(c.created_at) = :date';
    $params[':date'] = $_GET['date'];
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// --- Paginação ---
$porPagina = 5;
$paginaAtual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $porPagina;

// Contar total para paginação
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM comments c JOIN articles a ON c.article_id = a.id $whereClause");
$totalStmt->execute($params);
$totalComentarios = $totalStmt->fetchColumn();
$totalPaginas = ceil($totalComentarios / $porPagina);

// Buscar comentários
$stmt = $pdo->prepare("SELECT c.id, c.name, c.email, c.comment, c.is_verified, c.created_at, a.title AS article_title 
                       FROM comments c 
                       JOIN articles a ON c.article_id = a.id 
                       $whereClause
                       ORDER BY c.created_at DESC 
                       LIMIT :offset, :limit");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);

foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}

$stmt->execute();
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Comentários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        @media (max-width: 768px) {
            .table-container {
                display: none;
            }
            .card-container {
                display: block;
            }
        }
        @media (min-width: 769px) {
            .table-container {
                display: block;
            }
            .card-container {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <a href="../index.php" class="btn btn-outline-secondary mb-4"><i class="bi bi-arrow-left"></i> Voltar</a>
    <h2 class="mb-4">Gestão de Comentários</h2>

    <!-- Formulário de Filtro -->
    <form class="row g-3 mb-4" method="get">
        <div class="col-md-3">
            <input type="text" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" class="form-control" placeholder="Filtrar por email">
        </div>
        <div class="col-md-3">
            <input type="text" name="article" value="<?= htmlspecialchars($_GET['article'] ?? '') ?>" class="form-control" placeholder="Filtrar por artigo">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Todos</option>
                <option value="1" <?= (($_GET['status'] ?? '') === '1') ? 'selected' : '' ?>>Verificados</option>
                <option value="0" <?= (($_GET['status'] ?? '') === '0') ? 'selected' : '' ?>>Não verificados</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" class="form-control">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <!-- Tabela (Desktop) -->
    <div class="table-container table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-dark">
                <tr>
                    <th>Artigo</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Comentário</th>
                    <th>Estado</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comentarios as $comentario): ?>
                    <tr>
                        <td><?= htmlspecialchars($comentario['article_title']) ?></td>
                        <td><?= htmlspecialchars($comentario['name']) ?></td>
                        <td><?= htmlspecialchars($comentario['email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($comentario['comment'])) ?></td>
                        <td><?= $comentario['is_verified'] ? '✔️ Verificado' : '❌ Não verificado' ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($comentario['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDelete<?= $comentario['id'] ?>">
                                <i class="bi bi-trash"></i> Apagar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Cards (Mobile) -->
    <div class="card-container">
        <?php foreach ($comentarios as $comentario): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($comentario['article_title']) ?></h5>
                    <p><strong>Nome:</strong> <?= htmlspecialchars($comentario['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($comentario['email']) ?></p>
                    <p><strong>Comentário:</strong><br><?= nl2br(htmlspecialchars($comentario['comment'])) ?></p>
                    <p><strong>Estado:</strong> <?= $comentario['is_verified'] ? '✔️ Verificado' : '❌ Não verificado' ?></p>
                    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($comentario['created_at'])) ?></p>
                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDelete<?= $comentario['id'] ?>">
                        <i class="bi bi-trash"></i> Apagar
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= ($i == $paginaAtual) ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Modais de confirmação -->
<?php foreach ($comentarios as $comentario): ?>
<div class="modal fade" id="modalDelete<?= $comentario['id'] ?>" tabindex="-1" aria-labelledby="modalDeleteLabel<?= $comentario['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel<?= $comentario['id'] ?>">Confirmar Eliminação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem a certeza que deseja apagar o comentário de <strong><?= htmlspecialchars($comentario['name']) ?></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="?delete=<?= $comentario['id'] ?>" class="btn btn-danger">Apagar</a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>