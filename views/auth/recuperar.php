<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Recuperação de Senha - Comunidade Desportiva">
    <meta name="keywords" content="Recuperação de Senha, Comunidade Desportiva">
    <meta name="author" content="Comunidade Desportiva">
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Recuperar Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-1" style="max-width: 480px;">
    <h1 class="mb-4">Recuperar Senha</h1>

    <?php if ($mensagem): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
    <?php elseif ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Informa o teu email:</label>
            <input type="email" id="email" name="email" class="form-control" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100">Enviar link de recuperação</button>
    </form>

    <a href="login.php" class="d-block mt-3">Voltar ao login</a>
</div>
</body>
</html>


