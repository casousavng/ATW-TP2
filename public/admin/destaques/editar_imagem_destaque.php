<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

define('UPLOAD_DIR', BASE_PATH . '/public/uploads/destaque/');

// Processar uploads
$sucesso = false;
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem'])) {
    foreach ($_FILES['imagem']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['imagem']['error'][$index] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['imagem']['name'][$index], PATHINFO_EXTENSION);
            $nomeFicheiro = 'destaque_' . time() . '_' . $index . '.' . $extensao;
            $destino = UPLOAD_DIR . $nomeFicheiro;

            if (move_uploaded_file($tmpName, $destino)) {
                $pdo->prepare("INSERT INTO imagem_destaque (caminho) VALUES (?)")->execute([$nomeFicheiro]);
                $sucesso = true;
            } else {
                $erro = "Erro ao mover a imagem $index.";
            }
        }
    }
}

// Remover imagem
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT caminho FROM imagem_destaque WHERE id = ?");
    $stmt->execute([$id]);
    $caminho = $stmt->fetchColumn();

    if ($caminho && file_exists(UPLOAD_DIR . $caminho)) {
        unlink(UPLOAD_DIR . $caminho);
    }

    $pdo->prepare("DELETE FROM imagem_destaque WHERE id = ?")->execute([$id]);
    header("Location: editar_imagem_destaque.php");
    exit;
}

$imagens = $pdo->query("SELECT id, caminho, atualizado_em FROM imagem_destaque ORDER BY atualizado_em DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gerir Imagens em Destaque">
    <title>Gerir Imagens em Destaque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
    <h2>Imagens em Destaque</h2>

    <?php if ($sucesso): ?>
        <div class="alert alert-success">Imagens carregadas com sucesso!</div>
    <?php elseif ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label for="imagem" class="form-label">Escolher imagens (várias):</label>
            <input type="file" name="imagem[]" id="imagem" class="form-control" multiple required>
        </div>
        <button type="submit" class="btn btn-primary">Adicionar Imagens</button>
    </form>

    <div class="row">
        <?php foreach ($imagens as $img): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="/public/uploads/destaque/<?= htmlspecialchars($img['caminho']) ?>" class="card-img-top" style="max-height: 200px; object-fit: cover;">
                    <div class="card-body text-center">
                        <button 
                            class="btn btn-sm btn-danger"
                            data-bs-toggle="modal" 
                            data-bs-target="#confirmDeleteModal"
                            data-id="<?= $img['id'] ?>"
                            data-imgname="<?= htmlspecialchars($img['caminho']) ?>"
                        >
                            Remover
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de confirmação -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Remoção</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Tem certeza que deseja remover a imagem <strong id="modalImageName"></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" class="btn btn-danger" id="confirmDeleteBtn">Remover</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Quando o modal abre, configurar link do botão remover e o nome da imagem
    var confirmDeleteModal = document.getElementById('confirmDeleteModal');
    confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var imgId = button.getAttribute('data-id');
        var imgName = button.getAttribute('data-imgname');

        var modalImgName = confirmDeleteModal.querySelector('#modalImageName');
        var confirmBtn = confirmDeleteModal.querySelector('#confirmDeleteBtn');

        modalImgName.textContent = imgName;
        confirmBtn.href = "?delete=" + imgId;
    });
</script>
</body>
</html>