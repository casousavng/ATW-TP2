<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin(); // Verifica se o utilizador é admin

$errors = [];
$success = false;

// Adicionar novo campo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_field'])) {
    $field_name = trim($_POST['field_name'] ?? '');
    $field_type = trim($_POST['field_type'] ?? 'texto');

    if (empty($field_name)) {
        $errors[] = "O nome do campo é obrigatório.";
    } else {
        // Verificar duplicação
        $stmt = $pdo->prepare("SELECT * FROM extra_fields WHERE name = ?");
        $stmt->execute([$field_name]);

        if ($stmt->fetch()) {
            $errors[] = "Já existe um campo com este nome.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO extra_fields (name, type) VALUES (?, ?)");
            $stmt->execute([$field_name, $field_type]);
            $success = true;
        }
    }
}

// Remover campo
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $field_id = $_GET['delete'];

    // Remover diretamente da tabela extra_fields
    $stmt = $pdo->prepare("DELETE FROM extra_fields WHERE id = ?");
    $stmt->execute([$field_id]);
    $success = true;
}

// Buscar todos os campos existentes
$stmt = $pdo->query("SELECT * FROM extra_fields ORDER BY name ASC");
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
        <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
        <h2>Gestão de Campos Extra</h2>

        <?php if ($success): ?>
            <div class="alert alert-success">Operação realizada com sucesso.</div>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>

        <!-- Formulário para adicionar novo campo -->
        <form method="POST" class="bg-white p-4 shadow rounded mb-4">
            <h5 class="mb-3">Adicionar Novo Campo</h5>
            <div class="mb-3">
                <label for="field_name" class="form-label">Nome do Campo</label>
                <input type="text" class="form-control" id="field_name" name="field_name" required>
            </div>
            <div class="mb-3">
                <label for="field_type" class="form-label">Tipo</label>
                <select name="field_type" id="field_type" class="form-select">
                    <option value="texto">Texto</option>
                    <option value="numero">Número</option>
                    <option value="url">URL</option>
                </select>
            </div>
            <button type="submit" name="add_field" class="btn btn-primary">Adicionar Campo</button>
        </form>

        <!-- Lista de campos existentes -->
        <h5>Campos Existentes</h5>
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fields as $field): ?>
                    <tr>
                        <td><?= $field['id'] ?></td>
                        <td><?= htmlspecialchars($field['name']) ?></td>
                        <td><?= htmlspecialchars($field['type']) ?></td>
                        <td>
                            <a href="?delete=<?= $field['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tens a certeza que queres remover este campo?')">Remover</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>