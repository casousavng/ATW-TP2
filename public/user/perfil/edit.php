<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

checkLogin();

$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

// Carregar dados atuais do utilizador
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Carregar campos extras
$fields_stmt = $pdo->query("SELECT * FROM extra_fields");
$fields = $fields_stmt->fetchAll(PDO::FETCH_ASSOC);

// Carregar valores dos campos extras para este utilizador
$values_stmt = $pdo->prepare("SELECT * FROM user_extra_values WHERE user_id = ?");
$values_stmt->execute([$userId]);
$extra_values_raw = $values_stmt->fetchAll(PDO::FETCH_ASSOC);

$extra_values = [];
foreach ($extra_values_raw as $ev) {
    $extra_values[$ev['field_id']] = $ev['value'];
}

// Atualizar se o formulário for submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $birth_date = $_POST['birth_date'] ?? '';
    $nationality = $_POST['nationality'] ?? '';
    $country = $_POST['country'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';

    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$name || !$birth_date || !$email) {
        $errors[] = "Nome, data de nascimento e email são obrigatórios.";
    }

    if ($newPassword && $newPassword !== $confirmPassword) {
        $errors[] = "As passwords não coincidem.";
    }

    if (empty($errors)) {
        // Atualizar dados principais do utilizador
        $stmt = $pdo->prepare("UPDATE users SET name = ?, birth_date = ?, nationality = ?, country = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $birth_date, $nationality, $country, $phone, $email, $userId]);

        // Se nova password for fornecida, atualiza
        if ($newPassword) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $userId]);

            // Registra a atividade de alteração de senha
            $stmt = $pdo->prepare("INSERT INTO atividades (user_id, descricao, tipo_atividade) VALUES (?, ?, ?)");
            $stmt->execute([$userId, 'Senha alterada', 'edição']);
        }

        // Atualizar ou inserir os valores dos campos extras
        foreach ($fields as $field) {
            $field_id = $field['id'];
            $value = $_POST['extra'][$field_id] ?? '';

            // Verificar se o campo extra já existe para este utilizador
            $check = $pdo->prepare("SELECT id FROM user_extra_values WHERE user_id = ? AND field_id = ?");
            $check->execute([$userId, $field_id]);

            if ($check->fetch()) {
                // Atualizar valor do campo extra
                $update = $pdo->prepare("UPDATE user_extra_values SET value = ? WHERE user_id = ? AND field_id = ?");
                $update->execute([$value, $userId, $field_id]);
            } else {
                // Inserir novo valor para o campo extra
                $insert = $pdo->prepare("INSERT INTO user_extra_values (user_id, field_id, value) VALUES (?, ?, ?)");
                $insert->execute([$userId, $field_id, $value]);
            }
        }

        // Registra a atividade de edição do perfil
        $stmt = $pdo->prepare("INSERT INTO atividades (user_id, descricao, tipo_atividade) VALUES (?, ?, ?)");
        $stmt->execute([$userId, 'Perfil editado', 'edição']);

        $_SESSION['user_name'] = $name;
        $success = true;

        // Atualiza o $user para refletir os novos dados no formulário
        $user['name'] = $name;
        $user['birth_date'] = $birth_date;
        $user['nationality'] = $nationality;
        $user['country'] = $country;
        $user['phone'] = $phone;
        $user['email'] = $email;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


<div class="container mt-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>

    <?php if ($success): ?>
        <div class="alert alert-success">Perfil atualizado com sucesso!</div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="post" class="bg-light p-4 rounded shadow-sm">
        <!-- Campos principais -->
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="birth_date" class="form-label">Data de Nascimento</label>
            <input type="date" class="form-control" name="birth_date" id="birth_date" value="<?= htmlspecialchars($user['birth_date'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="nationality" class="form-label">Nacionalidade</label>
            <input type="text" class="form-control" name="nationality" id="nationality" value="<?= htmlspecialchars($user['nationality'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="country" class="form-label">País de Residência</label>
            <input type="text" class="form-control" name="country" id="country" value="<?= htmlspecialchars($user['country'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Telefone</label>
            <input type="tel" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        </div>

        <hr>

        <!-- Campos Extras -->
        <h4>Campos Extras</h4>
        <?php foreach ($fields as $field): ?>
            <div class="mb-3">
                <label class="form-label"><?= htmlspecialchars($field['name']) ?>:</label>
                <input type="text" class="form-control" name="extra[<?= $field['id'] ?>]" value="<?= htmlspecialchars($extra_values[$field['id']] ?? '') ?>">
            </div>
        <?php endforeach; ?>

        <hr>

        <!-- Seção para alteração de senha -->
        <h3>Alterar Password (opcional)</h3>
        <div class="mb-3">
            <label for="new_password" class="form-label">Nova Password</label>
            <input type="password" class="form-control" name="new_password" id="new_password">
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmar Nova Password</label>
            <input type="password" class="form-control" name="confirm_password" id="confirm_password">
        </div>

        <hr>

        <button type="submit" class="btn btn-primary">Guardar Alterações</button>
        <a href="../index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>