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
                $_SESSION['user'] = $user; // Guarda o utilizador na sessão

                // Registar atividade de login
                $stmtLog = $pdo->prepare("INSERT INTO atividades (user_id, descricao, tipo_atividade) VALUES (?, ?, ?)");
                $stmtLog->execute([$user['id'], 'Login efetuado (via 2FA)', 'login']);

                // ✅ Define cookie remember_2fa (válido por 30 dias)
                $cookie_name = 'remember_2fa';
                $cookie_value = hash('sha256', $user['id'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
                //setcookie($cookie_name, $cookie_value, time() + (30 * 60), "/", "", true, true);// Secure + HttpOnly
                setcookie($cookie_name, $cookie_value, time() + (30 * 60), "/", "", false, true);// NOT Secure + HttpOnly -> para testes locais

                // Limpa sessão temporária e token do utilizador
                unset($_SESSION['temp_user_id']);
                unset($_SESSION['2fa_user']);
                $stmt = $pdo->prepare("UPDATE users SET login_token = NULL, login_token_expires = NULL WHERE id = ?");
                $stmt->execute([$user_id]);

                // Redireciona para o dashboard correto
                header("Location: index.php");
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

    <p>Enviámos um código de verificação para o teu email. Por favor, insere-o abaixo.</p>
    <ul>
        <li>Verifica a pasta de spam se não encontrares o email.</li>
        <li>Após login com sucesso não será pedida Autenticação MFA durante 30min.</li>
        <li>Podes <strong>reenviar</strong> o código após a expiração (5 minutos).</li>
        <li>Ou <a href="login.php">tenta iniciar sessão novamente</a>.</li>
    </ul>

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
        <form action="reenviar_codigo.php" method="post">
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