<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Comunidade Desportiva</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color: #343a40; /* cinza escuro */
        }
        .navbar-custom .nav-link, .navbar-custom .navbar-brand {
            color: #f8f9fa;
        }
        .navbar-custom .nav-link:hover {
            color: #ced4da;
        }
        .search-box {
            display: none;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Função para exibir o campo de pesquisa de acordo com a página atual
            function toggleSearch() {
                const path = window.location.pathname;

                // Verifica se a URL contém "artigos", "noticias" ou "documentos" e exibe o campo de pesquisa correspondente
                if (path.includes('artigos')) {
                    document.getElementById('searchArtigos').style.display = 'block';
                } else if (path.includes('noticias')) {
                    document.getElementById('searchNoticias').style.display = 'block';
                } else if (path.includes('documentos')) {
                    document.getElementById('searchDocumentos').style.display = 'block';
                }
            }

            // Chama a função para ajustar o campo de pesquisa conforme a página
            toggleSearch();
        });
    </script>
</head>
<body>

<!-- Header Responsivo -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="../public/index.php">Comunidade Desportiva</a>
        <button class="navbar-toggler text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon text-white">&#9776;</span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="../public/artigos.php">Artigos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../public/noticias.php">Notícias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../public/documentos.php">Documentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../public/login.php">Entrar</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Conteúdo principal -->
<div class="container mt-4">