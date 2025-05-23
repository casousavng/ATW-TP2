<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Redefinir Senha - Comunidade Desportiva">
    <meta name="keywords" content="Redefinir Senha, Comunidade Desportiva">
    <meta name="author" content="Carlos Sousa, Gabriel Rocha, Miguel Magalhães">
    <link rel="icon" href="../assets/favicon/favicon.jpg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Redefinir Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 480px;">
    <h1 class="mb-4">Redefinir Senha</h1>

    <?php if ($mensagem): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
        <a href="login.php" class="btn btn-primary">Ir para login</a>
    <?php else: ?>
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="senha" class="form-label">Nova senha:</label>
                <input type="password" id="senha" name="senha" class="form-control" required minlength="6" autofocus>
            </div>

            <div class="mb-3">
                <label for="senha_confirm" class="form-label">Confirma a nova senha:</label>
                <input type="password" id="senha_confirm" name="senha_confirm" class="form-control" required minlength="6">
            </div>

            <button type="submit" class="btn btn-primary w-100">Alterar senha</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>