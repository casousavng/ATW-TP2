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

// Organiza os valores dos campos extras para fácil acesso
$extra_values = [];
foreach ($extra_values_raw as $ev) {
    $extra_values[$ev['field_id']] = $ev['value'];
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Área do Utilizador - Comunidade Desportiva" />
    <meta name="keywords" content="Área do Utilizador, Comunidade Desportiva, Artigos, Notícias, Documentos" />
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães" />
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/css/styles.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <title>Área do Utilizador</title>
</head>
<body class="bg-light">

    <!-- Cabeçalho -->
    <header class="bg-dark text-white py-3 mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="mb-0 fs-3">Área do Utilizador</h1>
              <a href="../index.php" class="btn btn-success">
                <i class="bi bi-house-door-fill me-1"></i> Voltar ao Início
            </a>
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
                        <p class="card-text">Consulta os teus dados pessoais e edita as informações quando necessário.</p>
                        <a href="perfil/edit.php" class="btn btn-primary">
                            <i class="bi bi-person-fill"></i> Editar Perfil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Artigos -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Meus Artigos</h5>
                        <p class="card-text">Cria e edita os artigos que partilhas com a comunidade.</p>
                        <a href="artigos/artigos.php" class="btn btn-info text-white">
                            <i class="bi bi-journal-text"></i> Gerir Artigos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Artigos/Notícias Guardadas -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Artigos/Notícias Guardadas</h5>
                        <p class="card-text">Consulta os artigos e notícias que guardaste para ler mais tarde.</p>
                        <a href="guardados.php" class="btn btn-warning text-white">
                            <i class="bi bi-bookmark-fill"></i> Ver Guardados
                        </a>
                    </div>
                </div>
            </div>

            <!-- Histórico -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Histórico de Atividades</h5>
                        <p class="card-text">Vê o histórico de atividades recentes realizadas na tua conta.</p>
                        <a href="perfil/atividades.php" class="btn btn-info text-white">
                            <i class="bi bi-clock-history"></i> Ver Histórico
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mb-4">Os teus dados:</h2>
        <ul class="list-group">
            <li class="list-group-item"><strong>Data de criação da conta:</strong> <?= htmlspecialchars($user['created_at']) ?></li>
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
        <br>

    </main>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>