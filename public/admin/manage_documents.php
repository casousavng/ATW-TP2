<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
checkAdmin();  // Garantir que é um administrador

// Buscar todos os documentos
$files = glob("../uploads/documentos/*.*");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Documentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container">
            <h1 class="mb-0">Gerenciar Documentos</h1>
        </div>
    </header>

    <div class="container mt-5">
        <a href="dashboard.php" class="btn btn-secondary mb-4">← Voltar ao Painel</a>

        <h3>Documentos Disponíveis</h3>
        <ul class="list-group">
            <?php foreach ($files as $file): ?>
                <li class="list-group-item">
                    <a href="<?= htmlspecialchars($file) ?>" download>
                        <?= basename($file) ?>
                    </a>
                    <a href="delete_document.php?file=<?= urlencode($file) ?>" class="btn btn-danger btn-sm float-end">Excluir</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <form method="post" enctype="multipart/form-data" class="mt-4">
            <h3>Adicionar Novo Documento</h3>
            <input type="file" name="document" required>
            <button type="submit" class="btn btn-primary mt-2">Adicionar Documento</button>
        </form>
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