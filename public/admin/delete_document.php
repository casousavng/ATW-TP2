<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();  // Garantir que é um administrador

$status = '';
$type = '';

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);

    if (file_exists($file)) {
        unlink($file);
        $status = 'Arquivo excluído com sucesso.';
        $type = 'success';
    } else {
        $status = 'Arquivo não encontrado!';
        $type = 'danger';
    }
} else {
    $status = 'Nenhum arquivo especificado!';
    $type = 'warning';
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Remover Arquivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <?php if ($status): ?>
            <div class="alert alert-<?= $type ?> text-center">
                <?= htmlspecialchars($status) ?>
            </div>
            <div class="text-center mt-4">
                <a href="manage_documents.php" class="btn btn-primary">Voltar para Gestão de Documentos</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>