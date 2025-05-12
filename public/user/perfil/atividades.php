<?php
define('BASE_PATH', dirname(__DIR__, 3));
require_once(BASE_PATH . '/includes/db.php');
require_once(BASE_PATH . '/includes/auth.php');

// Garante que apenas utilizadores autenticados acedem
checkLogin(); 

$userId = $_SESSION['user_id'];

// Obter as atividades do utilizador
$stmt = $pdo->prepare("SELECT * FROM atividades WHERE user_id = ? ORDER BY data DESC");
$stmt->execute([$userId]);
$atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Atividades - Área do Utilizador</title>
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


    <!-- Conteúdo principal -->
     
    <main class="container mt-5">
        <a href="../index.php" class="btn btn-outline-secondary mb-4">← Voltar</a>
        <h2 class="mb-4">Atividades Realizadas</h2>

        <!-- Lista de atividades -->
        <div class="list-group">
            <?php if ($atividades): ?>
                <?php foreach ($atividades as $atividade): ?>
                    <div class="list-group-item">
                        <h5 class="mb-1"><?= htmlspecialchars($atividade['descricao']) ?></h5>
                        <p class="mb-1"><strong>Data:</strong> <?= date('d/m/Y H:i:s', strtotime($atividade['data'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    <p class="mb-1">Não há atividades para mostrar.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>



    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>