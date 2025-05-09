<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');
checkAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = trim($_POST['field_name']);
    if ($field !== '') {
        $stmt = $pdo->prepare("INSERT INTO extra_fields (name) VALUES (?)");
        $stmt->execute([$field]);
    }
}

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

        <form method="post" class="mb-5">
            <div class="mb-3">
                <label for="field_name" class="form-label">Nome do novo campo:</label>
                <input type="text" name="field_name" id="field_name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Adicionar</button>
        </form>

        <h3>Campos Atuais:</h3>
        <ul class="list-group">
            <?php foreach ($fields as $f): ?>
                <li class="list-group-item"><?= htmlspecialchars($f['name']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>