<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
checkLogin();

$user_id = $_SESSION['user_id'];

// Buscar dados do utilizador
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar campos extra disponíveis
$fields = $pdo->query("SELECT * FROM extra_fields")->fetchAll(PDO::FETCH_ASSOC);

// Buscar valores atuais dos campos extra do utilizador
$values_stmt = $pdo->prepare("SELECT * FROM user_extra_values WHERE user_id = ?");
$values_stmt->execute([$user_id]);
$extra_values_raw = $values_stmt->fetchAll(PDO::FETCH_ASSOC);

$extra_values = [];
foreach ($extra_values_raw as $ev) {
    $extra_values[$ev['field_id']] = $ev['value'];
}

// Atualizar dados do utilizador
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, date_of_birth = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $dob, $email, $user_id]);

    // Atualizar campos extra
    foreach ($fields as $field) {
        $field_id = $field['id'];
        $value = $_POST['extra'][$field_id] ?? '';

        $check = $pdo->prepare("SELECT id FROM user_extra_values WHERE user_id = ? AND field_id = ?");
        $check->execute([$user_id, $field_id]);

        if ($check->fetch()) {
            $update = $pdo->prepare("UPDATE user_extra_values SET value = ? WHERE user_id = ? AND field_id = ?");
            $update->execute([$value, $user_id, $field_id]);
        } else {
            $insert = $pdo->prepare("INSERT INTO user_extra_values (user_id, field_id, value) VALUES (?, ?, ?)");
            $insert->execute([$user_id, $field_id, $value]);
        }
    }

    header("Location: profile.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>O Meu Perfil</title>
    <!-- Incluindo o Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Cabeçalho -->
<header class="bg-primary text-white py-3">
    <div class="container">
        <h1 class="mb-0">O Meu Perfil</h1>
    </div>
</header>

<!-- Conteúdo principal -->
<div class="container mt-5">
    <h2>Bem-vindo(a), <?= htmlspecialchars($user['name']) ?></h2>
    <a href="../logout.php" class="btn btn-danger mb-4">Terminar Sessão</a>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Perfil atualizado com sucesso!</div>
    <?php endif; ?>

    <form method="post" class="bg-light p-4 rounded shadow-sm">
        <!-- Campo Nome -->
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <!-- Campo Data de Nascimento -->
        <div class="mb-3">
            <label for="dob" class="form-label">Data de Nascimento</label>
            <input type="date" class="form-control" name="dob" id="dob" value="<?= htmlspecialchars($user['date_of_birth']) ?>" required>
        </div>

        <!-- Campo Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <!-- Campos Extra -->
        <h3>Campos Extra</h3>
        <?php foreach ($fields as $field): ?>
            <div class="mb-3">
                <label for="extra[<?= $field['id'] ?>]" class="form-label"><?= htmlspecialchars($field['name']) ?></label>
                <input type="text" class="form-control" name="extra[<?= $field['id'] ?>]" value="<?= htmlspecialchars($extra_values[$field['id']] ?? '') ?>">
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary">Guardar Alterações</button>
    </form>
</div>

<!-- Rodapé -->
<footer class="bg-dark text-white py-3 mt-5">
    <div class="container text-center">
        <p>&copy; <?= date('Y') ?> Comunidade Desportiva</p>
    </div>
</footer>

<!-- Incluindo o JS do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>