<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

$uploadDir = BASE_PATH . '/public/uploads/documentos/';
$relativeDir = '/uploads/documentos/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0775, true);

// Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file = $_FILES['document'];
    $filename = basename($file['name']);
    $targetPath = $uploadDir . $filename;
    $nomePersonalizado = trim($_POST['nome_personalizado']);

    if (!$nomePersonalizado) {
        $uploadError = "Por favor, insere um nome para o documento.";
    } elseif (is_uploaded_file($file['tmp_name'])) {
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $stmt = $pdo->prepare("INSERT INTO documentos (nome_personalizado, nome_ficheiro) VALUES (?, ?)");
            $stmt->execute([$nomePersonalizado, $filename]);
            $uploadSuccess = true;
        } else {
            $uploadError = "Erro ao mover o ficheiro.";
        }
    } else {
        $uploadError = "Upload inválido.";
    }
}

$docs = $pdo->query("SELECT * FROM documentos ORDER BY data_upload DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gerir Documentos">
    <title>Gerir Documentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <main class="container mt-1">
        <a href="../index.php" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <br>
        <!-- Botão para mostrar/esconder formulário -->
        <button id="toggleFormBtn" class="btn btn-success mb-4">
            <i class="bi bi-plus-lg me-1"></i> Adicionar Documento
        </button>

                <!-- Formulário escondido inicialmente -->
        <div id="formContainer" style="display: none;">
            <h3 class="mb-3">Adicionar Novo Documento</h3>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nome_personalizado" class="form-label">Nome do Documento</label>
                    <input type="text" name="nome_personalizado" id="nome_personalizado" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="file" name="document" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Adicionar Documento
                </button>
            </form>
            <hr>
        </div>

        <?php if (!empty($uploadSuccess)): ?>
            <div class="alert alert-success">Documento carregado com sucesso!</div>
        <?php elseif (!empty($uploadError)): ?>
            <div class="alert alert-danger"><?= $uploadError ?></div>
        <?php endif; ?>

        <h3 class="mb-3">Documentos Disponíveis</h3>
        <ul class="list-group mb-4">
            <?php if (empty($docs)): ?>
                <li class="list-group-item">Sem documentos partilhados.</li>
            <?php else: ?>
                <?php foreach ($docs as $doc): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($doc['nome_personalizado']) ?></strong><br>
                            <a href="<?= htmlspecialchars($relativeDir . $doc['nome_ficheiro']) ?>" download>
                                <?= htmlspecialchars($doc['nome_ficheiro']) ?>
                            </a>
                        </div>
                        <a href="delete_document.php?id=<?= $doc['id'] ?>" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i> Excluir
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

    </main>
</div>

<script>
    const btn = document.getElementById('toggleFormBtn');
    const form = document.getElementById('formContainer');

    if (btn && form) {
        btn.addEventListener('click', function () {
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                btn.innerHTML = '<i class="bi bi-x-lg me-1"></i> Fechar Formulário';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-danger');
            } else {
                form.style.display = 'none';
                btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Adicionar Documento';
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-success');
            }
        });
    }
</script>
</body>
</html>