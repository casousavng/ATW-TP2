<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

// Adicionar novo campo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $field = trim($_POST['field_name']);
    $type = trim($_POST['field_type']);

    if ($field !== '' && $type !== '') {
        $stmt = $pdo->prepare("INSERT INTO extra_fields (name, type) VALUES (?, ?)");
        $stmt->execute([$field, $type]);
    }
}

// Apagar campo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $fieldId = intval($_POST['field_id']);

    // Primeiro apagar dados associados
    $pdo->prepare("DELETE FROM extra_fields WHERE id = ?")->execute([$fieldId]);

    // Depois apagar o campo
    $pdo->prepare("DELETE FROM extra_fields WHERE id = ?")->execute([$fieldId]);
}

// Buscar campos existentes
$fields = $pdo->query("SELECT * FROM extra_fields ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Campos Extra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <a href="index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>

    <h1 class="mb-4">Campos Extra de Perfil</h1>

    <!-- Formulário para adicionar novo campo -->
    <form method="post" class="mb-5">
        <input type="hidden" name="action" value="add">
        <div class="mb-3">
            <label for="field_name" class="form-label">Nome do novo campo:</label>
            <input type="text" name="field_name" id="field_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="field_type" class="form-label">Tipo do campo:</label>
            <select name="field_type" id="field_type" class="form-select" required>
                <option value="">-- Escolhe um tipo --</option>
                <option value="text">Texto</option>
                <option value="number">Número</option>
                <option value="date">Data</option>
                <option value="email">Email</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Adicionar</button>
    </form>

    <!-- Listagem dos campos existentes -->
    <h3>Campos Atuais:</h3>
    <ul class="list-group">
        <?php foreach ($fields as $f): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= htmlspecialchars($f['name']) ?></strong>
                    <span class="badge bg-secondary"><?= htmlspecialchars($f['type']) ?></span>
                </div>
                <form method="post" class="d-inline m-0">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="field_id" value="<?= $f['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tens a certeza que queres apagar este campo? Isto também apagará os dados dos utilizadores relacionados.')">
                        Apagar
                    </button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>