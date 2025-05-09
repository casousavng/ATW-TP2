<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

$field_id = $_GET['id'] ?? null;
if (!$field_id) {
    header("Location: manage_extra_fields.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM extra_fields WHERE id = ?");
$stmt->execute([$field_id]);
$field = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$field) {
    echo "Campo não encontrado.";
    exit;
}

$errors = [];
$success = false;

// Atualizar o nome do campo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field_name = $_POST['field_name'] ?? '';

    if (empty($field_name)) {
        $errors[] = "O nome do campo é obrigatório.";
    } else {
        // Atualizar o campo
        $stmt = $pdo->prepare("UPDATE extra_fields SET name = ? WHERE id = ?");
        $stmt->execute([$field_name, $field_id]);
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Campo Extra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="manage_extra_fields.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
        <h1 class="mb-4">Editar Campo Extra</h1>

        <!-- Mensagens de erro e sucesso -->
        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                Campo extra atualizado com sucesso!
            </div>
        <?php endif; ?>
        
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endforeach; ?>

        <!-- Formulário para editar o campo -->
        <form method="POST" class="bg-white p-4 shadow rounded">
            <div class="mb-3">
                <label for="field_name" class="form-label">Nome do Campo:</label>
                <input type="text" name="field_name" id="field_name" class="form-control" value="<?= htmlspecialchars($field['name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>