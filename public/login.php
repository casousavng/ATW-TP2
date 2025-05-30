<?php
session_start();

// public/login.php (CONTROLLER)
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
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM login_attempts 
            WHERE email = ? 
              AND ip_address = ? 
              AND attempt_time > (NOW() - INTERVAL 10 MINUTE)
        ");
        $stmt->execute([$email, $ip]);
        $attempts = $stmt->fetchColumn();

        if ($attempts >= 5) {
            $errors[] = "Demasiadas tentativas falhadas. Tenta novamente daqui a 10 minutos.";
        } else {
            // Tenta encontrar o utilizador
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $isPasswordCorrect = $user && password_verify($password, $user['password']);

            // 👉 Regista tentativa falhada se as credenciais forem inválidas
            if (!$isPasswordCorrect) {
                $stmt = $pdo->prepare("INSERT INTO login_attempts (email, ip_address, attempt_time) VALUES (?, ?, NOW())");
                $stmt->execute([$email, $ip]);

                // Log da tentativa falhada
                $stmt = $pdo->prepare("INSERT INTO login_logs (user_id, email, ip_address, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['id'] ?? null, $email, $ip, 'fail']);

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
                // ✅ Verifica se o cookie de 2FA está presente e válido
                $cookie_name = 'remember_2fa';
                $user_hash = hash('sha256', $user['id'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

                if (isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] === $user_hash) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    $_SESSION['user'] = $user;

                    // Loga atividade
                    $stmtLog = $pdo->prepare("INSERT INTO atividades (user_id, descricao, tipo_atividade) VALUES (?, ?, ?)");
                    $stmtLog->execute([$user['id'], 'Login efetuado (2FA lembrado por cookie)', 'login']);

                    header("Location: index.php");
                    exit;
                }

                // 🔐 Senão, segue para envio de código 2FA
                $code = random_int(100000, 999999);
                $expires = date('Y-m-d H:i:s', time() + 300);

                $stmt = $pdo->prepare("UPDATE users SET login_token = ?, login_token_expires = ? WHERE id = ?");
                $stmt->execute([strval($code), $expires, $user['id']]);

                sendVerificationCode($user['email'], $user['name'], $code);

                $_SESSION['temp_user_id'] = $user['id'];
                $_SESSION['2fa_user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'is_admin' => $user['is_admin']
                ];

                // Log de sucesso
                $stmt = $pdo->prepare("INSERT INTO login_logs (user_id, email, ip_address, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['id'], $email, $ip, 'success']);

                header("Location: 2fa.php");
                exit;
            }
        }
    }
}

include '../includes/header.php';
include '../views/auth/login.php';
include '../includes/footer.php';