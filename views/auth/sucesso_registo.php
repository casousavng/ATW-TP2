<?php
include '../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Comunidade Desportiva - Registo com Sucesso">
    <meta name="keywords" content="Comunidade Desportiva, Registo, Sucesso">
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães">
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/style_header.css">
    <title>Registo com Sucesso</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light d-flex flex-column min-vh-100">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow rounded-4 border-success">
                <div class="card-body text-center p-5">
                    <i class="bi bi-check-circle-fill text-success display-4 mb-3"></i>
                    <h2 class="card-title mb-4">Registo Efetuado com Sucesso!</h2>
                    <p class="card-text fs-5">Enviámos um email de verificação. Por favor verifica a tua conta antes de iniciar sessão.</p>
                    <a href="/public/login.php" class="btn btn-primary mt-4">Ir para Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
</html>