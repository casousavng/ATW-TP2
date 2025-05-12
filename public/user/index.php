<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

// Garante que apenas utilizadores autenticados acedem
checkLogin(); 

$userId = $_SESSION['user_id'];

// Buscar dados do utilizador
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

// Organize os valores dos campos extras para fácil acesso
$extra_values = [];
foreach ($extra_values_raw as $ev) {
    $extra_values[$ev['field_id']] = $ev['value'];
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Área do Utilizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main.container {
            flex: 1;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 1rem;
            margin-top: auto;
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Cabeçalho -->
    <header class="bg-dark text-white py-3 mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="mb-0 fs-3">Área do Utilizador</h1>
            <a href="../logout.php" class="btn btn-danger">Terminar Sessão</a>
        </div>
    </header>

    <!-- Conteúdo principal -->
    <main class="container mb-5">
        <h2 class="mb-4">Bem-vindo(a), <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
        <div class="row">
            <!-- Dados Pessoais -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Dados Pessoais</h5>
                        <p class="card-text">Consulte os seus dados pessoais e edite as informações quando necessário.</p>
                        <a href="perfil/edit.php" class="btn btn-primary">Editar Perfil</a>
                    </div>
                </div>
            </div>

            <!-- Artigos -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Meus Artigos</h5>
                        <p class="card-text">Crie e edite os artigos que partilha com a comunidade.</p>
                        <a href="artigos/artigos.php" class="btn btn-info">Gerir Artigos</a>
                    </div>
                </div>
            </div>

            <!-- Histórico -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Histórico de Atividades</h5>
                        <p class="card-text">Veja o histórico de atividades recentes realizadas na sua conta.</p>
                        <a href="perfil/atividades.php" class="btn btn-info">Ver Histórico</a>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mb-4">Os teus dados:</h2>
        <ul class="list-group">
            <li class="list-group-item"><strong>Nome:</strong> <?= htmlspecialchars($user['name']) ?></li>
            <li class="list-group-item"><strong>Data de nascimento:</strong> <?= htmlspecialchars($user['birth_date']) ?></li>
            <li class="list-group-item"><strong>Nacionalidade:</strong> <?= htmlspecialchars($user['nationality']) ?></li>
            <li class="list-group-item"><strong>País de residência:</strong> <?= htmlspecialchars($user['country']) ?></li>
            <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
            <li class="list-group-item"><strong>Telefone:</strong> <?= htmlspecialchars($user['phone']) ?></li>
            <?php foreach ($fields as $field): ?>
                <li class="list-group-item"><strong><?= htmlspecialchars($field['name']) ?>:</strong> <?= htmlspecialchars($extra_values[$field['id']] ?? 'Não definido') ?></li>
            <?php endforeach; ?>
        </ul>

    </main>



    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>