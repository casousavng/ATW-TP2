<?php
// Recuperar Senha
require_once '../includes/db.php';
require_once '../includes/mailer.php'; 

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if ($email) {
        // Verificar se o email existe no banco
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(16));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expira]);

            $link = "http://localhost:3000/public/reset_senha.php?token=" . urlencode($token);

            $htmlBody = "
                <p>Olá <strong>" . htmlspecialchars($user['name']) . "</strong>,</p>
                <p>Solicitas-te a recuperação de senha. Clique no link abaixo para redefinir a tua senha:</p>
                <p><a href='$link'>$link</a></p>
                <p>Este link é válido por 1 hora.</p>
                <p>Se não solicitaste essa ação, podes ignorar este email.</p>
            ";

            if (sendEmail($email, $user['name'], 'Recuperação de Senha', $htmlBody, 'Suporte Comunidade')) {
                $mensagem = "Enviamos um email com o link para redefinir a tua senha.";
            } else {
                $erro = "Erro ao enviar email. Por favor, tenta novamente mais tarde.";
            }
        } else {
            $erro = "Email não encontrado.";
        }
    } else {
        $erro = "Por favor, informa um email válido.";
    }
}

// No final do controller, inclua a view.
// As variáveis $searchQuery e $noticias estarão disponíveis na view.
include '../includes/header.php'; // Se tiver um header comum
include '../views/auth/recuperar.php'; // A view específica da listagem de notícias
include '../includes/footer.php'; // Se tiver um footer comum



?>


