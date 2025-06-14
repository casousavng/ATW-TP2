<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once BASE_PATH . '/includes/db.php';
require_once BASE_PATH . '/includes/auth.php';
checkAdmin();

$sucesso = false;
$erro = '';

// Exclusão da notícia via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];

    $stmt = $pdo->prepare("SELECT imagem FROM noticias WHERE id = ?");
    $stmt->execute([$delete_id]);
    $noticia = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($noticia) {
        if ($noticia['imagem']) {
            $imagemPath = BASE_PATH . '/public/uploads/noticias/' . $noticia['imagem'];
            if (file_exists($imagemPath)) {
                unlink($imagemPath);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->execute([$delete_id]);

        header('Location: add_noticia.php');
        exit;
    } else {
        $erro = "Notícia não encontrada para exclusão.";
    }
}

// Processar o envio do formulário para adicionar notícia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem = $_FILES['imagem'];
        $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
        $nomeFicheiro = 'noticia_' . time() . '.' . $extensao;
        $destino = BASE_PATH . '/public/uploads/noticias/' . $nomeFicheiro;

        if (move_uploaded_file($imagem['tmp_name'], $destino)) {
            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, imagem, texto, data_criacao) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$titulo, $nomeFicheiro, $texto]);
            $sucesso = true;
        } else {
            $erro = "Erro ao carregar a imagem.";
        }
    } else {
        $erro = "Erro ao carregar a imagem.";
    }
}

$stmt = $pdo->query("SELECT * FROM noticias ORDER BY data_criacao DESC");
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestão de Notícias">
    <title>Lista de Notícias</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .btn-fixed-width {
            min-width: 100px;
            text-align: center;
        }
        body {
            overflow-x: hidden;
        }
        textarea {
            resize: none;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <h2 class="mt-1">Todas as Notícias</h2>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Título</th>
                <th>Data de Inserção</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($noticias)): ?>
            <tr>
                <td colspan="3" class="text-center">Não há notícias publicadas de momento.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($noticias as $noticia): ?>
                <tr>
                    <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($noticia['data_criacao'])) ?></td>
                    <td>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>

                            <button 
                                class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#confirmDeleteModal"
                                data-id="<?= $noticia['id'] ?>"
                                data-titulo="<?= htmlspecialchars($noticia['titulo'], ENT_QUOTES) ?>"
                                data-texto="<?= htmlspecialchars($noticia['texto'], ENT_QUOTES) ?>"
                                data-imagem="<?= htmlspecialchars($noticia['imagem'], ENT_QUOTES) ?>"
                            >
                                <i class="bi bi-trash"></i> Apagar
                            </button>

                            <a href="ocultar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-sm btn-fixed-width <?= $noticia['visivel'] ? 'btn-secondary' : 'btn-success' ?>">
                                <i class="bi <?= $noticia['visivel'] ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                <?= $noticia['visivel'] ? 'Ocultar' : 'Mostrar' ?>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <hr>
    <h2>Adicionar Nova Notícia</h2>

    <?php if ($sucesso): ?>
        <div class="alert alert-success">Notícia adicionada com sucesso!</div>
    <?php elseif (!empty($erro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título:</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem:</label>
            <input type="file" name="imagem" id="imagem" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="texto" class="form-label">Texto:</label>
            <textarea name="texto" id="texto" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Adicionar Notícia
        </button>
        <button type="reset" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Limpar Campos
        </button>
    </form>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="post" id="deleteForm">
                <input type="hidden" name="delete_id" id="delete_id" value="">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza de que deseja excluir esta notícia?</p>
                    <h5 id="modalTitulo"></h5>
                    <p id="modalTexto"></p>
                    <img id="modalImagem" class="img-fluid rounded mb-3" style="max-height: 300px; display: none;" alt="Imagem da notícia">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Sim, excluir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var confirmDeleteModal = document.getElementById('confirmDeleteModal');
    confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        var id = button.getAttribute('data-id');
        var titulo = button.getAttribute('data-titulo');
        var texto = button.getAttribute('data-texto');
        var imagem = button.getAttribute('data-imagem');

        document.getElementById('delete_id').value = id;
        document.getElementById('modalTitulo').textContent = titulo;
        document.getElementById('modalTexto').textContent = texto;

        var modalImagem = document.getElementById('modalImagem');
        if (imagem) {
            modalImagem.src = "/public/uploads/noticias/" + imagem;
            modalImagem.style.display = 'block';
        } else {
            modalImagem.style.display = 'none';
            modalImagem.src = '';
        }
    });
</script>
</body>
</html>