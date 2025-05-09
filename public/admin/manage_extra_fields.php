<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();  // Garantir que é um administrador

$errors = [];
$success = false;

// Adicionar um novo campo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_field'])) {
    $field_name = $_POST['field_name'] ?? '';
    
    if (empty($field_name)) {
        $errors[] = "O nome do campo é obrigatório.";
    } else {
        // Verificar se o campo já existe
        $stmt = $pdo->prepare("SELECT * FROM extra_fields WHERE name = ?");
        $stmt->execute([$field_name]);
        if ($stmt->fetch()) {
            $errors[] = "Já existe um campo com este nome.";
        } else {
            // Inserir o novo campo
            $stmt = $pdo->prepare("INSERT INTO extra_fields (name) VALUES (?)");
            $stmt->execute([$field_name]);
            $success = true;
        }
    }
}

// Remover um campo extra
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $field_id = $_GET['delete'];
    
    // Verificar se o campo está em uso
    $stmt = $pdo->prepare("SELECT * FROM user_extra_values WHERE field_id = ?");
    $stmt->execute([$field_id]);
    
    if ($stmt->rowCount() > 0) {
        $errors[] = "Não é possível remover este campo porque está em uso.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM extra_fields WHERE id = ?");
        $stmt->execute([$field_id]);
        $success = true;
    }
}

// Obter todos os campos extra
$stmt = $pdo->query("SELECT * FROM extra_fields ORDER BY name");
$fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Campos Extra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="index.php" class="btn btn-outline-secondary mb-4">← Voltar ao Painel</a>
        <h1 class="mb-4">Gestão de Campos Extra</h1>

        <!-- Mensagens de erro e sucesso -->
        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                Campo extra adicionado/removido com sucesso!
            </div>
        <?php endif; ?>
        
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endforeach; ?>

        <!-- Formulário para adicionar novo campo -->
        <form method="POST" class="bg-white p-4 shadow rounded mb-4">
            <div class="mb-3">
                <label for="field_name" class="form-label">Nome do Novo Campo:</label>
                <input type="text" name="field_name" id="field_name" class="form-control" required>
            </div>
            <button type="submit" name="add_field" class="btn btn-primary">Adicionar Campo</button>
        </form>

        <!-- Listar campos existentes -->
        <h4>Campos Existentes</h4>
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome do Campo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($fields as $field): ?>
                <tr>
                    <td><?= $field['id'] ?></td>
                    <td><?= htmlspecialchars($field['name']) ?></td>
                    <td>
                        <a href="edit_field.php?id=<?= $field['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="?delete=<?= $field['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este campo?')">Remover</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>