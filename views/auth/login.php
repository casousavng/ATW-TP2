<?php
// views/auth/login.php (VIEW)

// Não há lógica de processamento de formulário ou DB aqui.
// Apenas exibe o HTML e variáveis que já foram preparadas pelo controller.
?>

<main class="container mt-1">
    <h2>Iniciar Sessão</h2>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="post" class="bg-light p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>

    
    <p class="mt-3">
        Ainda não tens conta? <a href="register.php">Regista-te aqui</a><br>
        Esquecestes-te a tua senha? <a href="../../public/recuperar_senha.php">Recuperar senha</a>
    </p>
</main>