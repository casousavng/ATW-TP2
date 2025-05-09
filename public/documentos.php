<?php
$files = glob("../uploads/documentos/*.*");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Documentos</title>
    <!-- Incluindo o Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Cabeçalho -->
<header class="bg-primary text-white py-3">
    <div class="container">
        <h1 class="mb-0">Documentos Públicos</h1>
        <nav class="mt-2">
            <a href="index.php" class="text-white text-decoration-none me-3">Início</a>
            <a href="noticias.php" class="text-white text-decoration-none me-3">Notícias</a>
            <a href="documentos.php" class="text-white text-decoration-none me-3">Documentos</a>
            <a href="login.php" class="text-white text-decoration-none">Entrar</a>
        </nav>
    </div>
</header>

<!-- Seção de documentos -->
<div class="container mt-5">
    <a href="index.php" class="btn btn-secondary mb-4">← Voltar</a>

    <h2>Lista de Documentos</h2>
    <ul class="list-group">
        <?php foreach ($files as $file): ?>
            <li class="list-group-item">
                <a href="<?= htmlspecialchars($file) ?>" download class="text-decoration-none">
                    <?= basename($file) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
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