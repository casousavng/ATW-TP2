<?php
// public/login.php (CONTROLLER)

session_start();
require_once '../includes/db.php';
require_once '../includes/mailer.php'; // para enviar o código e email de verificação

$errors = []; // Esta variável será passada para a view

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $errors[] = "Preenche todos os campos.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_verified']) {
                // Gera novo token de verificação
                $newToken = bin2hex(random_bytes(16));

                // Atualiza o token na base de dados
                $stmt = $pdo->prepare("UPDATE users SET verification_token = ? WHERE id = ?");
                $stmt->execute([$newToken, $user['id']]);

                // Envia o email de verificação
                if (sendVerificationEmail($user['email'], $user['name'], $newToken)) {
                    $errors[] = "A tua conta ainda não foi verificada. Enviámos um novo email de verificação.";
                } else {
                    $errors[] = "Erro ao reenviar o email de verificação. Tenta mais tarde.";
                }
            } else {
                // Gera token de 6 dígitos
                $code = random_int(100000, 999999);
                $expires = date('Y-m-d H:i:s', time() + 300); // expira em 5 minutos

                // Guarda token no user
                $stmt = $pdo->prepare("UPDATE users SET login_token = ?, login_token_expires = ? WHERE id = ?");
                $stmt->execute([$code, $expires, $user['id']]);

                // Envia o código por email
                sendVerificationCode($user['email'], $user['name'], $code);

                // Guarda dados temporariamente na sessão
                $_SESSION['temp_user_id'] = $user['id'];
                $_SESSION['2fa_user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'is_admin' => $user['is_admin']
                ];
                $_SESSION['2fa_code'] = $code;

                // Redireciona para o 2FA
                header("Location: 2fa.php");
                exit;
            }
        } else {
            $errors[] = "Credenciais inválidas.";
        }
    }
}

// No final do controller, inclua a view.
// A variável $errors estará disponível na view.
include '../includes/header.php'; // Se o header for um include comum a todas as páginas
include '../views/auth/login.php'; // A view específica do login
include '../includes/footer.php'; // Se o footer for um include comum a todas as páginas