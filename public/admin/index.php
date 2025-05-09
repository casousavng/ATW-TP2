<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Painel de Administração</h1>
            <a href="../logout.php" class="btn btn-danger">Terminar Sessão</a>
        </div>

        <div class="list-group shadow-sm">
            <a href="users.php" class="list-group-item list-group-item-action">
                Gestão de Utilizadores
            </a>
            <a href="fields.php" class="list-group-item list-group-item-action">
                Campos Extra
            </a>
            <!-- <a href="content.php" class="list-group-item list-group-item-action">
                Gestão de Conteúdos
            </a> -->
        </div>
    </div>
</body>
</html>