<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin(); // Garante que o utilizador é administrador
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração</title>
    <meta name="description" content="Painel de administração para gestão de utilizadores, artigos, documentos e mais.">
    <meta name="keywords" content="administração, gestão, utilizadores, artigos, documentos, notícias">
    <meta name="author" content="Comunidade Desportiva">
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsivo -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import do Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main.container {
            flex: 1;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 1rem;
            margin-top: auto;
            text-align: center;
        }

        /* Ajuste extra para ecrãs pequenos */
        @media (max-width: 576px) {
            .card {
                margin-bottom: 1.5rem;
            }

            .card-body {
                padding: 1rem;
            }

            h5.card-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body class="bg-light">

    <!-- Cabeçalho -->
    <header class="bg-dark text-white py-3 mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="mb-0 fs-3">Painel de Administração</h1>
            <a href="../index.php" class="btn btn-success">
                <i class="bi bi-house-door-fill me-1"></i> Voltar ao Início
            </a>
        </div>
    </header>

    <!-- Conteúdo principal -->
    <main class="container mb-5">
        <h2 class="mb-4">Bem-vindo(a), <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
        <div class="row">
            <!-- Utilizadores -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Gestão de Utilizadores</h5>
                        <p class="card-text">Faz a gestão dos membros da tua comunidade. Edita os dados ou faz alteração de status.</p>
                        <a href="gerir_utilizadores/users.php" class="btn btn-primary">
                            <i class="bi bi-gear-fill me-1"></i> Gerir
                        </a>
                    </div>
                </div>
            </div>

            <!-- Campos Extra -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Campos Extra</h5>
                        <p class="card-text">Configura campos personalizados para os perfis dos teus utilizadores.</p>
                        <a href="gerir_utilizadores/manage_extra_fields.php" class="btn btn-primary"><i class="bi bi-gear-fill me-1"></i> Gerir
                        </a>
                    </div>
                </div>
            </div>

            <!-- Artigos -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Gestão de Artigos</h5>
                        <p class="card-text">Faz a gestão dos artigos publicados pela tua comunidade.</p>
                        <a href="gerir_artigos/artigos.php" class="btn btn-primary"><i class="bi bi-gear-fill me-1"></i> Gerir</a>
                    </div>
                </div>
            </div>

            <!-- Documentos -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Gestão de Documentos</h5>
                        <p class="card-text">Faz upload, edita ou remove documentos partilhados com a comunidade.</p>
                        <a href="documentos/manage_documents.php" class="btn btn-primary"><i class="bi bi-gear-fill me-1"></i> Gerir</a>
                    </div>
                </div>
            </div>

            <!-- Imagem Destaque -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Gestão de Imagem em Destaque</h5>
                        <p class="card-text">Faz upload, edita ou remove a imagem em destaque.</p>
                        <a href="destaques/editar_imagem_destaque.php" class="btn btn-primary"><i class="bi bi-gear-fill me-1"></i> Gerir</a>
                    </div>
                </div>
            </div>

            <!-- Notícias -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Gestão de Notícias</h5>
                        <p class="card-text">Cria, edita ou remove notícias partilhadas com a comunidade.</p>
                        <a href="noticias/add_noticia.php" class="btn btn-primary"><i class="bi bi-gear-fill me-1"></i> Gerir</a>
                    </div>
                </div>
            </div>

            <!-- Gestão de Comentários -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Gestão de Comentários</h5>
                        <p class="card-text">Vê todos os comentários feitos nos artigos, com opções para verificar ou remover.</p>
                        <a href="gerir_artigos/gestao_comentarios.php" class="btn btn-primary">
                            <i class="bi bi-chat-dots me-1"></i> Ver Comentários
                        </a>
                    </div>
                </div>
            </div>
        
            <!-- Gestão de Acessos -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Gestão de Acessos</h5>
                        <p class="card-text">Consulta os registos de login e tentativas de intrusão.</p>
                        <a href="gerir_utilizadores/gestao_acessos.php" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Ver Registos
                        </a>
                    </div>
                </div>
            </div>
        </div>
        </main>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>