<?php
session_start();
require_once '../includes/db.php';

$errors = [];

if (!isset($_SESSION['temp_user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['temp_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = $_POST['code'] ?? '';

    if (!preg_match('/^\d{6}$/', $input_code)) {
        $errors[] = "O código deve ter exatamente 6 dígitos.";
    } else {
        // Vai buscar o utilizador
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = "Utilizador não encontrado.";
        } else {
            $now = date('Y-m-d H:i:s');

            // Verifica o token e validade
            if (
                $user['login_token'] === $input_code &&
                $user['login_token_expires'] !== null &&
                $now <= $user['login_token_expires']
            ) {
                // Login finalizado com sucesso
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['is_admin'] = $user['is_admin'];

                // Limpa sessão temporária e token do utilizador
                unset($_SESSION['temp_user_id']);
                $stmt = $pdo->prepare("UPDATE users SET login_token = NULL, login_token_expires = NULL WHERE id = ?");
                $stmt->execute([$user_id]);

                // Redireciona para o dashboard correto
                if ($_SESSION['is_admin']) {
                    header("Location: admin/index.php");
                } else {
                    header("Location: user/index.php");
                }
                exit;
            } else {
                $errors[] = "Código inválido ou expirado. Verifica o teu email ou solicita novo login.";
            }
        }
    }
}

// Vai buscar o utilizador para obter a data de expiração
$stmt = $pdo->prepare("SELECT login_token_expires FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$expiresAtMs = isset($user['login_token_expires']) ? strtotime($user['login_token_expires']) * 1000 : null;

?>

<?php include('../includes/header.php'); ?>

<main class="container mt-1">
    <h2>Confirmação Multiplo-fator</h2>

    <p>Um código de verificação foi enviado para o teu email. Por favor, verifica o teu email e insere o código abaixo.</br>
    Se não recebeste o email, verifica a pasta de spam ou clica no botão abaixo para reenviar o código.</br>
    O código expira em 5 minutos.</br>
    Se não conseguiste iniciar sessão, por favor <a href="login.php">tenta novamente</a>.</p>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="post" class="bg-light p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="code" class="form-label">Código de 6 dígitos</label>
            <input type="text" class="form-control" name="code" id="code" maxlength="6" required pattern="\d{6}">
        </div>

        <button type="submit" class="btn btn-primary">Verificar</button>
    </form>

<div id="timer" class="mt-3 text-muted"></div>

<div id="resend-container" class="mt-3" style="display: none;">
    <form action="resend_code.php" method="post">
        <button type="submit" class="btn btn-link">Reenviar código</button>
    </form>
</div>

<script>
    const expiresAt = <?= $expiresAtMs ?? 'null' ?>;

    const timerDisplay = document.getElementById('timer');
    const resendContainer = document.getElementById('resend-container');

    if (!expiresAt) {
        timerDisplay.textContent = "Não foi possível obter o tempo de expiração.";
    } else {
        function updateTimer() {
            const now = Date.now();
            const remaining = Math.floor((expiresAt - now) / 1000);

            if (remaining <= 0) {
                clearInterval(timerInterval);
                timerDisplay.textContent = "O código expirou.";
                resendContainer.style.display = "block";
            } else {
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                timerDisplay.textContent = `Código expira em ${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
        }

        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);
    }
</script>

</main>

<?php include('../includes/footer.php'); ?>