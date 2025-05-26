<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

// Filtro por verificação
$filter = $_GET['filter'] ?? '';

$query = "SELECT * FROM users";
if ($filter === 'verificados') {
    $query .= " WHERE is_verified = 1";
} elseif ($filter === 'nao_verificados') {
    $query .= " WHERE is_verified = 0";
}
$query .= " ORDER BY name";

$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lista de Utilizadores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Lista de utilizadores do sistema com opções de edição.">
    <meta name="keywords" content="utilizadores, gestão, administração, editar, lista">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .user-card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: white;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }

        body {
            overflow-x: hidden;
        }

        p {
            margin: 2px 0;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
    <h1 class="mb-4">Lista de Utilizadores</h1>

    <!-- Filtro -->
    <form method="get" class="mb-4 d-flex align-items-center gap-2">
        <label for="filter" class="form-label mb-0">Filtrar:</label>
        <select name="filter" id="filter" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="verificados" <?= $filter === 'verificados' ? 'selected' : '' ?>>Apenas Verificados</option>
            <option value="nao_verificados" <?= $filter === 'nao_verificados' ? 'selected' : '' ?>>Apenas Não Verificados</option>
        </select>
    </form>

    <!-- Tabela para dispositivos médios e grandes -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Administrador?</th>
                    <th>Status</th>
                    <th>Verificado?</th>
                    <th>Data de Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['is_admin'] ? 'Sim' : 'Não' ?></td>
                    <td><?= $user['status'] == 'ativo' ? 'Ativo' : 'Inativo' ?></td>
                    <td><?= $user['is_verified'] ? 'Sim' : 'Não' ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle"></i> Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Versão mobile com cards -->
    <div class="d-md-none">
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <p><strong>Nome:</strong> <?= htmlspecialchars($user['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Administrador?</strong> <?= $user['is_admin'] ? 'Sim' : 'Não' ?></p>
                <p><strong>Status:</strong> <?= $user['status'] == 'ativo' ? 'Ativo' : 'Inativo' ?></p>
                <p><strong>Verificado?</strong> <?= $user['is_verified'] ? 'Sim' : 'Não' ?></p>
                <p><strong>Data de Criação:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary mt-2">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>