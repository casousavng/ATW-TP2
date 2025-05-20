<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: users.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilizador não encontrado.";
    exit;
}

$adminCountStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 1");
$adminCount = $adminCountStmt->fetchColumn();
$isOnlyAdmin = $user['is_admin'] && $adminCount == 1;

$fields = $pdo->query("SELECT * FROM extra_fields")->fetchAll(PDO::FETCH_ASSOC);

$values_stmt = $pdo->prepare("SELECT * FROM user_extra_values WHERE user_id = ?");
$values_stmt->execute([$id]);
$extra_values_raw = $values_stmt->fetchAll(PDO::FETCH_ASSOC);

$extra_values = [];
foreach ($extra_values_raw as $ev) {
    $extra_values[$ev['field_id']] = $ev['value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if ($isOnlyAdmin && !$is_admin) {
        $is_admin = 1;
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, is_admin = ? WHERE id = ?");
    $stmt->execute([$name, $email, $is_admin, $id]);

    foreach ($fields as $field) {
        $field_id = $field['id'];
        $value = $_POST['extra'][$field_id] ?? '';

        $check = $pdo->prepare("SELECT id FROM user_extra_values WHERE user_id = ? AND field_id = ?");
        $check->execute([$id, $field_id]);

        if ($check->fetch()) {
            $update = $pdo->prepare("UPDATE user_extra_values SET value = ? WHERE user_id = ? AND field_id = ?");
            $update->execute([$value, $id, $field_id]);
        } else {
            $insert = $pdo->prepare("INSERT INTO user_extra_values (user_id, field_id, value) VALUES (?, ?, ?)");
            $insert->execute([$id, $field_id, $value]);
        }
    }

    if (!empty($_POST['new_field_name'])) {
        $new_field_name = $_POST['new_field_name'];
        $new_field_type = $_POST['new_field_type'];

        $stmt = $pdo->prepare("INSERT INTO extra_fields (name, type) VALUES (?, ?)");
        $stmt->execute([$new_field_name, $new_field_type]);
    }

    header("Location: users.php");
    exit;
}

if (isset($_GET['confirm_action'])) {
    if ($_GET['confirm_action'] === 'inactive' && !$isOnlyAdmin) {
        $new_status = $user['status'] === 'ativo' ? 'inativo' : 'ativo';
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        header("Location: edit_user.php?id=$id");
        exit;
    }

    if ($_GET['confirm_action'] === 'delete' && !$isOnlyAdmin) {
        $stmt = $pdo->prepare("DELETE FROM user_extra_values WHERE user_id = ?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Utilizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <style>
        .status-indicator {
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            margin-top: 4px;
        }
        .circle {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        .circle.green {
            background-color: #28a745;
        }
        .circle.red {
            background-color: #dc3545;
        }
        .header-with-status {
            display: flex;
            align-items: center;
            gap: 16px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <a href="users.php" class="btn btn-outline-secondary mb-4">← Voltar</a>

    <div class="header-with-status mb-4">
        <h1 class="mb-0">Editar Utilizador</h1>
        <span class="status-indicator">
            <span class="circle <?= $user['status'] === 'ativo' ? 'green' : 'red' ?>"></span>
            <?= ucfirst($user['status']) ?>
        </span>
    </div>

    <form method="post" class="bg-white p-4 shadow rounded">
        <div class="mb-3">
            <label for="name" class="form-label">Nome:</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" <?= $user['is_admin'] ? 'checked' : '' ?> <?= $isOnlyAdmin ? 'disabled' : '' ?>>
            <label class="form-check-label" for="is_admin">Administrador</label>
            <?php if ($isOnlyAdmin): ?>
                <div class="text-danger mt-1">Este é o único administrador. Não podes remover este privilégio.</div>
            <?php endif; ?>
        </div>

        <h4>Campos Extra</h4>
        <?php foreach ($fields as $field): ?>
            <div class="mb-3">
                <label class="form-label"><?= htmlspecialchars($field['name']) ?>:</label>
                <input type="<?= htmlspecialchars($field['type'] ?? 'text') ?>" 
                       class="form-control" 
                       value="<?= htmlspecialchars($extra_values[$field['id']] ?? '') ?>" 
                       disabled>
            </div>
        <?php endforeach; ?>

        <h4>Adicionar Novo Campo</h4>
        <div class="mb-3">
            <label for="new_field_name" class="form-label">Nome do Novo Campo:</label>
            <input type="text" class="form-control" id="new_field_name" name="new_field_name">
        </div>

        <div class="mb-3">
            <label for="new_field_type" class="form-label">Tipo do Novo Campo:</label>
            <select class="form-control" id="new_field_type" name="new_field_type">
                <option value="text">Texto</option>
                <option value="number">Número</option>
                <option value="url">URL</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Guardar</button>
    </form>

    <div class="mt-4">
        <?php if (!$isOnlyAdmin): ?>
            <?php if ($user['status'] === 'ativo'): ?>
                <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#confirmModal" data-action="inactive">Inativar Utilizador</button>
            <?php else: ?>
                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#confirmModal" data-action="inactive">Ativar Utilizador</button>
            <?php endif; ?>

            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmModal" data-action="delete">Apagar Utilizador</button>
        <?php else: ?>
            <div class="alert alert-danger mt-3">Não é possível inativar ou apagar o único administrador.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirmar Ação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="modal-body-text">
        Tens a certeza que desejas continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="confirmActionBtn" class="btn btn-danger">Confirmar</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const confirmModal = document.getElementById('confirmModal');
    const confirmBtn = document.getElementById('confirmActionBtn');
    const modalText = document.getElementById('modal-body-text');

    confirmModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const action = button.getAttribute('data-action');

        let text = '';
        if (action === 'delete') {
            text = 'Tens a certeza que desejas apagar este utilizador? Esta ação não pode ser desfeita.';
        } else if (action === 'inactive') {
            text = 'Tens a certeza que desejas alterar o estado deste utilizador?';
        }

        modalText.textContent = text;
        confirmBtn.href = `?id=<?= $user['id'] ?>&confirm_action=${action}`;
    });
});
</script>
</body>
</html>