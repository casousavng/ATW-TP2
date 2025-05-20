<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

$stmt = $pdo->query("SELECT * FROM users ORDER BY name");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lista de Utilizadores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
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
                <p><strong>ID:</strong> <?= $user['id'] ?></p>
                <p><strong>Nome:</strong> <?= htmlspecialchars($user['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Administrador?</strong> <?= $user['is_admin'] ? 'Sim' : 'Não' ?></p>
                <p><strong>Status:</strong> <?= $user['status'] == 'ativo' ? 'Ativo' : 'Inativo' ?></p>
                <p><strong>Data de Criação:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>