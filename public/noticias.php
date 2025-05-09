<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Notícias</title>
    <!-- Incluindo o Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Cabeçalho -->
<header class="bg-primary text-white py-3">
    <div class="container">
        <h1 class="mb-0">Notícias da Comunidade</h1>
        <nav class="mt-2">
            <a href="index.php" class="text-white text-decoration-none me-3">Início</a>
            <a href="noticias.php" class="text-white text-decoration-none me-3">Notícias</a>
            <a href="documentos.php" class="text-white text-decoration-none me-3">Documentos</a>
            <a href="login.php" class="text-white text-decoration-none">Entrar</a>
        </nav>
    </div>
</header>

<!-- Seção de conteúdo -->
<div class="container mt-5">
    <a href="index.php" class="btn btn-secondary mb-4">← Voltar</a>

    <!-- Artigo 1 -->
    <article class="mb-4">
        <h2>🏃 Torneio Anual de Atletismo</h2>
        <p>Realiza-se no próximo mês o torneio com inscrições abertas até ao fim desta semana.</p>
        <img src="imagens/torneio.jpg" alt="Torneio" class="img-fluid">
    </article>

    <!-- Artigo 2 -->
    <article>
        <h2>🚴 Nova pista de ciclismo inaugurada</h2>
        <p>A nova pista está pronta a ser usada pelos nossos atletas!</p>
        <img src="imagens/pista.jpg" alt="Pista de ciclismo" class="img-fluid">
    </article>
</div>

<!-- Rodapé -->
<footer class="bg-dark text-white py-3 mt-5">
    <div class="container text-center">
        <p>&copy; <?= date('Y') ?> Comunidade Desportiva</p>
    </div>
</footer>

<!-- Incluindo o JS do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>