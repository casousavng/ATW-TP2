<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();  // Função que verifica se o usuário é um administrador

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container">
            <h1 class="mb-0">Painel de Administração</h1>
        </div>
    </header>

    <div class="container mt-5">
        <h2>Bem-vindo(a), Administrador(a)</h2>
        <div class="row">
            <!-- Link para Gerenciar Usuários -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gerenciar Usuários</h5>
                        <p class="card-text">Visualize, edite e exclua usuários registrados na comunidade.</p>
                        <a href="manage_users.php" class="btn btn-primary">Gerenciar</a>
                    </div>
                </div>
            </div>

            <!-- Link para Gerenciar Documentos -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gerenciar Documentos</h5>
                        <p class="card-text">Adicione, edite ou exclua documentos disponibilizados na comunidade.</p>
                        <a href="manage_documents.php" class="btn btn-primary">Gerenciar</a>
                    </div>
                </div>
            </div>

            <!-- Link para Ver Estatísticas -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Estatísticas</h5>
                        <p class="card-text">Visualize as estatísticas da comunidade, como número de usuários e documentos.</p>
                        <a href="statistics.php" class="btn btn-primary">Ver Estatísticas</a>
                    </div>
                </div>
            </div>

            <!-- Link para Gerenciar Configurações -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Configurações</h5>
                        <p class="card-text">Alterar as configurações gerais da comunidade.</p>
                        <a href="settings.php" class="btn btn-primary">Configurações</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-3 mt-5">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Comunidade Desportiva</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>