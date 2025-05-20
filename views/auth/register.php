<?php
// views/auth/register.php (VIEW)

// Não há lógica de processamento de formulário ou DB aqui.
// Apenas exibe o HTML e variáveis que já foram preparadas pelo controller ($errors).
?>

<main class="container mt-3">
    <h1 class="mb-4">Registo de Utilizador</h1>
    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Nome*</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="birth_date" class="form-label">Data de Nascimento*</label>
            <input type="date" name="birth_date" id="birth_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="nationality" class="form-label">Nacionalidade</label>
            <input type="text" name="nationality" id="nationality" class="form-control">
        </div>
        <div class="mb-3">
            <label for="country" class="form-label">País</label>
            <input type="text" name="country" id="country" class="form-control">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email*</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Telefone</label>
            <input type="text" name="phone" id="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password*</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="confirm" class="form-label">Confirmar Password*</label>
            <input type="password" name="confirm" id="confirm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registar</button>
    </form>
</main>