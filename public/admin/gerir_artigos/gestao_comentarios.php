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

// Marcar como resolvido
if (isset($_GET['resolver']) && is_numeric($_GET['resolver'])) {
    $stmt = $pdo->prepare("UPDATE comments SET resolvido = TRUE WHERE id = ?");
    $stmt->execute([$_GET['resolver']]);
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

if (!empty($_GET['filtro_denuncia'])) {
    if ($_GET['filtro_denuncia'] === 'sem_denuncia') {
        $where[] = 'c.denunciado = 0';
    } elseif ($_GET['filtro_denuncia'] === 'denunciado') {
        $where[] = 'c.denunciado = 1 AND c.resolvido = 0';
    } elseif ($_GET['filtro_denuncia'] === 'resolvido') {
        $where[] = 'c.denunciado = 1 AND c.resolvido = 1';
    }
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

// Buscar comentários - incluindo o campo denunciado
$stmt = $pdo->prepare("SELECT c.id, c.name, c.email, c.comment, c.is_verified, c.created_at, c.resolvido, c.denunciado, a.title AS article_title 
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
        @media (max-width: 768px) {
        .card-container .btn {
            margin-bottom: 0 !important; /* Remove margens verticais */
            flex-grow: 1; /* Faz os botões crescerem para ocupar espaço disponível */
            text-align: center;
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
    <div class="col-md-2">
        <input type="text" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" class="form-control" placeholder="Filtrar por email">
    </div>
    <div class="col-md-2">
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
        <select name="filtro_denuncia" class="form-select">
            <option value="">Todas denúncias</option>
            <option value="sem_denuncia" <?= (($_GET['filtro_denuncia'] ?? '') === 'sem_denuncia') ? 'selected' : '' ?>>Sem denúncia</option>
            <option value="denunciado" <?= (($_GET['filtro_denuncia'] ?? '') === 'denunciado') ? 'selected' : '' ?>>Denunciado</option>
            <option value="resolvido" <?= (($_GET['filtro_denuncia'] ?? '') === 'resolvido') ? 'selected' : '' ?>>Resolvido</option>
        </select>
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
                    <th>Resolução</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comentarios as $comentario): ?>
                    <tr class="<?= ($comentario['denunciado'] && $comentario['resolvido']) ? 'table-success' : '' ?>">
                        <td><?= htmlspecialchars($comentario['article_title']) ?></td>
                        <td><?= htmlspecialchars($comentario['name']) ?></td>
                        <td><?= htmlspecialchars($comentario['email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($comentario['comment'])) ?></td>
                        <td><?= $comentario['is_verified'] ? '✔️ Verificado' : '❌ Não verificado' ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($comentario['created_at'])) ?></td>
                        <td>
                            <?php
                            if ($comentario['denunciado']) {
                                echo $comentario['resolvido']
                                    ? '<span class="badge bg-success">Resolvido</span>'
                                    : '<span class="badge bg-warning text-dark">Pendente</span>';
                            } else {
                                echo '<span class="badge bg-secondary">Sem denúncia</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($comentario['denunciado'] && !$comentario['resolvido']): ?>
                                <a href="?resolver=<?= $comentario['id'] ?>" class="btn btn-sm btn-success mb-1" title="Marcar como resolvido">
                                    <i class="bi bi-check2-circle"></i>
                                </a>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-info mb-1" data-bs-toggle="modal" data-bs-target="#modalDetalhes<?= $comentario['id'] ?>" title="Ver detalhes">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger mb-1" data-bs-toggle="modal" data-bs-target="#modalDelete<?= $comentario['id'] ?>" title="Apagar">
                                <i class="bi bi-trash"></i>
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
            <div class="card mb-3 <?= ($comentario['denunciado'] && $comentario['resolvido']) ? 'border-success' : '' ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($comentario['article_title']) ?></h5>
                    <p><strong>Nome:</strong> <?= htmlspecialchars($comentario['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($comentario['email']) ?></p>
                    <p><strong>Comentário:</strong><br><?= nl2br(htmlspecialchars($comentario['comment'])) ?></p>
                    <p><strong>Estado:</strong> <?= $comentario['is_verified'] ? '✔️ Verificado' : '❌ Não verificado' ?></p>
                    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($comentario['created_at'])) ?></p>
                    <p><strong>Resolução:</strong>
                        <?php
                        if ($comentario['denunciado']) {
                            echo $comentario['resolvido'] ? '<span class="badge bg-success">Resolvido</span>' : '<span class="badge bg-warning text-dark">Pendente</span>';
                        } else {
                            echo '<span class="badge bg-secondary">Sem denúncia</span>';
                        }
                        ?>
                    </p>
                    <?php if ($comentario['denunciado'] && !$comentario['resolvido']): ?>
                        <a href="?resolver=<?= $comentario['id'] ?>" class="btn btn-sm btn-success mb-1">
                            <i class="bi bi-check2-circle"></i> Marcar como resolvido
                        </a>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-info mb-1" data-bs-toggle="modal" data-bs-target="#modalDetalhes<?= $comentario['id'] ?>">
                        <i class="bi bi-eye"></i> Ver detalhes
                    </button>
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
            <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                <li class="page-item <?= ($p === $paginaAtual) ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $p])) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

</div>

<!-- Modais para detalhes e delete -->
<?php foreach ($comentarios as $comentario): ?>
<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes<?= $comentario['id'] ?>" tabindex="-1" aria-labelledby="modalDetalhesLabel<?= $comentario['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalhesLabel<?= $comentario['id'] ?>">Detalhes do Comentário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p><strong>Artigo:</strong> <?= htmlspecialchars($comentario['article_title']) ?></p>
                <p><strong>Nome:</strong> <?= htmlspecialchars($comentario['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($comentario['email']) ?></p>
                <p><strong>Comentário:</strong><br><?= nl2br(htmlspecialchars($comentario['comment'])) ?></p>
                <p><strong>Estado:</strong> <?= $comentario['is_verified'] ? '✔️ Verificado' : '❌ Não verificado' ?></p>
                <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($comentario['created_at'])) ?></p>
                <p><strong>Denunciado:</strong> <?= $comentario['denunciado'] ? 'Sim' : 'Não' ?></p>
                <p><strong>Resolução:</strong>
                    <?php
                    if ($comentario['denunciado']) {
                        echo $comentario['resolvido'] ? '✅ Sim' : '❌ Não';
                    } else {
                        echo 'Sem denúncia';
                    }
                    ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="modalDelete<?= $comentario['id'] ?>" tabindex="-1" aria-labelledby="modalDeleteLabel<?= $comentario['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-danger text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel<?= $comentario['id'] ?>">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir o comentário do(a) <strong><?= htmlspecialchars($comentario['name']) ?></strong> no artigo <strong><?= htmlspecialchars($comentario['article_title']) ?></strong>?
            </div>
            <div class="modal-footer">
                <a href="?delete=<?= $comentario['id'] ?>" class="btn btn-light text-danger">Excluir</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>