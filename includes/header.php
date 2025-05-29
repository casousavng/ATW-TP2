<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Comunidade Desportiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Comunidade Desportiva - Artigos, Notícias e partilha de Documentos">
    <meta name="keywords" content="Comunidade Desportiva, Artigos, Notícias, Documentos">
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães">
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style_header.css">
    <!-- Manifest -->
    <link rel="manifest" href="../public/manifest.json">
    <!-- Icon -->
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/jpeg" sizes="512x512">
    <!-- Meta para PWA -->
    <meta name="theme-color" content="#000000">
    <!-- Meta para iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Exemplo">
    <link rel="apple-touch-icon" href="/public/icons/icon-192.png">

</head>

<body>

<!-- Header Responsivo -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="../public/index.php">
            <img src="../assets/favicon/favicon.jpg" width="30" height="30" class="d-inline-block align-top" alt="">
            Comunidade Desportiva
        </a>
        <button class="navbar-toggler text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
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

                <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $_SESSION['is_admin'] ? '../public/admin/index.php' : '../public/user/index.php' ?>">
                            Área Pessoal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" style="color:rgb(235, 40, 59) !important;" href="../public/logout.php">SAIR</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" style="color:rgb(25, 135, 51) !important;" href="../public/login.php">ENTRAR</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Conteúdo principal -->
<div class="container mt-4">