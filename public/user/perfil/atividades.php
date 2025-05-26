<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

// Garante que apenas utilizadores autenticados acedem
checkLogin();

$userId = $_SESSION['user_id'];

// Paginação
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

// Filtrar por tipo de atividade (opcional)
$tipoFiltro = $_GET['tipo'] ?? '';

// Construir query com filtro
$query = "SELECT * FROM atividades WHERE user_id = ?";
$params = [$userId];

if ($tipoFiltro && in_array($tipoFiltro, ['criação', 'edição', 'artigo_adicionado', 'login', 'logout', 'outro'])) {
    $query .= " AND tipo_atividade = ?";
    $params[] = $tipoFiltro;
}

// Adicionar ordenação, LIMIT e OFFSET — usar prepared statements apenas para valores, colocar LIMIT/OFFSET como inteiros para evitar erros
$limit = (int)$itemsPerPage;
$offsetInt = (int)$offset;
$query .= " ORDER BY data DESC LIMIT $limit OFFSET $offsetInt";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar total de atividades para paginação
$countQuery = "SELECT COUNT(*) FROM atividades WHERE user_id = ?";
$countParams = [$userId];
if ($tipoFiltro && in_array($tipoFiltro, ['criação', 'edição', 'artigo_adicionado', 'login', 'logout', 'outro'])) {
    $countQuery .= " AND tipo_atividade = ?";
    $countParams[] = $tipoFiltro;
}
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($countParams);
$totalAtividades = $countStmt->fetchColumn();
$totalPages = ceil($totalAtividades / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Área do Utilizador - Comunidade Desportiva" />
    <meta name="keywords" content="Área do Utilizador, Comunidade Desportiva, Atividades" />
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães" />
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/css/styles.css" />
    <title>Atividades - Área do Utilizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

    <main class="container mt-4">
        <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
        <h2 class="mb-4">Atividades Realizadas</h2>

        <!-- Filtro de tipo -->
        <form method="get" class="mb-4">
            <label for="tipo" class="form-label">Filtrar por Tipo de Atividade:</label>
            <select name="tipo" id="tipo" class="form-select" onchange="this.form.submit()">
                <option value="" <?= $tipoFiltro === '' ? 'selected' : '' ?>>Todos</option>
                <option value="criação" <?= $tipoFiltro === 'criação' ? 'selected' : '' ?>>Criação</option>
                <option value="edição" <?= $tipoFiltro === 'edição' ? 'selected' : '' ?>>Edição</option>
                <option value="artigo_adicionado" <?= $tipoFiltro === 'artigo_adicionado' ? 'selected' : '' ?>>Artigo Adicionado</option>
                <option value="login" <?= $tipoFiltro === 'login' ? 'selected' : '' ?>>Login</option>
                <option value="logout" <?= $tipoFiltro === 'logout' ? 'selected' : '' ?>>Logout</option>
                <option value="outro" <?= $tipoFiltro === 'outro' ? 'selected' : '' ?>>Outro</option>
            </select>
        </form>

        <!-- Lista de atividades -->
        <div class="list-group">
            <?php if ($atividades): ?>
                <?php foreach ($atividades as $atividade): ?>
                    <div class="list-group-item">
                        <h5 class="mb-1"><?= htmlspecialchars($atividade['descricao']) ?></h5>
                        <p class="mb-1"><strong>Data:</strong> <?= date('d/m/Y H:i:s', strtotime($atividade['data'])) ?></p>
                        <small><strong>Tipo:</strong> <?= htmlspecialchars($atividade['tipo_atividade']) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    <p class="mb-1">Não há atividades para mostrar.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Navegação de páginas" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&tipo=<?= urlencode($tipoFiltro) ?>" aria-label="Página anterior">&laquo;</a>
                    </li>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&tipo=<?= urlencode($tipoFiltro) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&tipo=<?= urlencode($tipoFiltro) ?>" aria-label="Próxima página">&raquo;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>