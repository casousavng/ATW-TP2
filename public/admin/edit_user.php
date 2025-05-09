<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: users.php");
    exit;
}

// Buscar as informações do usuário
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilizador não encontrado.";
    exit;
}

// Buscar os campos extra
$fields = $pdo->query("SELECT * FROM extra_fields")->fetchAll(PDO::FETCH_ASSOC);

// Buscar os valores dos campos extra para o usuário
$values_stmt = $pdo->prepare("SELECT * FROM user_extra_values WHERE user_id = ?");
$values_stmt->execute([$id]);
$extra_values_raw = $values_stmt->fetchAll(PDO::FETCH_ASSOC);

$extra_values = [];
foreach ($extra_values_raw as $ev) {
    $extra_values[$ev['field_id']] = $ev['value'];
}

// Lidar com o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar informações do usuário
    $name = $_POST['name'];
    $email = $_POST['email'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Atualiza o usuário na tabela users
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, is_admin = ? WHERE id = ?");
    $stmt->execute([$name, $email, $is_admin, $id]);

    // Atualizar ou inserir valores dos campos extras
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

    // Adicionar um novo campo extra
    if (!empty($_POST['new_field_name'])) {
        $new_field_name = $_POST['new_field_name'];
        $new_field_type = $_POST['new_field_type'];

        // Inserir o novo campo na tabela extra_fields
        $stmt = $pdo->prepare("INSERT INTO extra_fields (name, type) VALUES (?, ?)");
        $stmt->execute([$new_field_name, $new_field_type]);
    }

    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Utilizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="users.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
        <h1 class="mb-4">Editar Utilizador</h1>

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
                <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" <?= $user['is_admin'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_admin">
                    Administrador
                </label>
            </div>

            <h4>Campos Extra</h4>
            <?php foreach ($fields as $field): ?>
                <div class="mb-3">
                    <label class="form-label"><?= htmlspecialchars($field['name']) ?>:</label>
                    <input type="<?= htmlspecialchars($field['type'] ?? 'text') ?>" class="form-control" name="extra[<?= $field['id'] ?>]" value="<?= htmlspecialchars($extra_values[$field['id']] ?? '') ?>">
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
    </div>
</body>
</html>