<?php
// public/login.php (CONTROLLER)

session_start();
require_once '../includes/db.php';
require_once '../includes/mailer.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];

    if (!$email || !$password) {
        $errors[] = "Preenche todos os campos.";
    } else {
        // Proteção brute-force
        $time_window = date('Y-m-d H:i:s', time() - 600); // últimos 10 minutos
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = ? AND ip_address = ? AND attempt_time > ?");
        $stmt->execute([$email, $ip, $time_window]);
        $attempts = $stmt->fetchColumn();

        if ($attempts >= 5) {
            $errors[] = "Demasiadas tentativas falhadas. Tenta novamente daqui a 10 minutos.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $isPasswordCorrect = $user && password_verify($password, $user['password']);

            // Logging da tentativa
            $stmt = $pdo->prepare("INSERT INTO login_logs (user_id, email, ip_address, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user['id'] ?? null, $email, $ip, $isPasswordCorrect ? 'success' : 'fail']);

            if (!$isPasswordCorrect) {
                // Regista tentativa falhada
                $stmt = $pdo->prepare("INSERT INTO login_attempts (email, ip_address, attempt_time) VALUES (?, ?, NOW())");
                $stmt->execute([$email, $ip]);

                $errors[] = "Credenciais inválidas.";
            } elseif (!$user['is_verified']) {
                // Reenvia email de verificação
                $newToken = bin2hex(random_bytes(16));
                $stmt = $pdo->prepare("UPDATE users SET verification_token = ? WHERE id = ?");
                $stmt->execute([$newToken, $user['id']]);

                if (sendVerificationEmail($user['email'], $user['name'], $newToken)) {
                    $errors[] = "A tua conta ainda não foi verificada. Enviámos um novo email de verificação.";
                } else {
                    $errors[] = "Erro ao reenviar o email de verificação. Tenta mais tarde.";
                }
            } else {
                // Gera código de 6 dígitos (texto simples)
                $code = random_int(100000, 999999);
                $expires = date('Y-m-d H:i:s', time() + 300); // expira em 5 minutos

                // Guarda código simples e validade
                $stmt = $pdo->prepare("UPDATE users SET login_token = ?, login_token_expires = ? WHERE id = ?");
                $stmt->execute([strval($code), $expires, $user['id']]);

                // Envia código por email
                sendVerificationCode($user['email'], $user['name'], $code);

                // Guarda dados temporários na sessão para 2FA
                $_SESSION['temp_user_id'] = $user['id'];
                $_SESSION['2fa_user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'is_admin' => $user['is_admin']
                ];

                header("Location: 2fa.php");
                exit;
            }
        }
    }
}

include '../includes/header.php';
include '../views/auth/login.php';
include '../includes/footer.php';