<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../public/vendor/autoload.php';
require_once __DIR__ . '/../includes/env_loader.php';
loadEnv(__DIR__ . '/../.env');


// Configuração do MailHog para testes
function configureMailer(): PHPMailer {
    $mail = new PHPMailer(true);

    // Configurações SMTP para MailHog
    $mail->isSMTP();
    $mail->Host       = 'localhost';
    $mail->SMTPAuth   = false;           // MailHog não precisa de auth
    $mail->Port       = 1025;
    $mail->CharSet = 'UTF-8';

    return $mail;
}

/*
// configuração do Mailtrap (exemplo)
function configureMailer(): PHPMailer {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = getenv('MAIL_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('MAIL_USER');
    $mail->Password   = getenv('MAIL_PASS');
    $mail->Port       = getenv('MAIL_PORT');
    $mail->CharSet    = 'UTF-8';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    return $mail;
}
}
*/

function sendEmail($toEmail, $toName, $subject, $htmlBody, $fromName = 'Sistema', $fromEmail = 'no-reply@comunidadedesportiva.com') {
    try {
        $mail = configureMailer();
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Aqui poderias fazer log do erro se necessário: $mail->ErrorInfo
        return false;
    }
}

function sendVerificationEmail($email, $name, $token) {

    $verificationLink = getBaseUrl() . "/includes/verificar_conta.php?token=" . urlencode($token);
    //$verificationLink = "http://localhost:3000/includes/verificar_conta.php?token=" . urlencode($token);

    $body = "
        Se bem-vindo a nossa comunidade, <strong>$name</strong>.<br><br>
        Por favor confirma o teu registo clicando no link abaixo:<br>
        <a href='$verificationLink'>$verificationLink</a><br><br>
        Obrigado!
    ";

    return sendEmail($email, $name, 'Confirme o seu registo', $body, 'Registo Conta');
}

function sendVerificationCode($email, $name, $code) {
    $body = "
        Olá <strong>$name</strong>,<br><br>
        Alguém (possivelmente tu) tentou iniciar sessão.<br>
        Usa o seguinte código para confirmar o login:<br><br>
        <h2 style='color:#2e6c80;'>$code</h2>
        <br>Este código é válido por 5 minutos.<br><br>
        Se não foste tu, ignora este email.
    ";

    return sendEmail($email, $name, 'Código de verificação de login', $body, 'Confirmação de Login');
}

function sendPasswordResetEmail($email, $name, $token) {

    $link = getBaseUrl() . "/public/reset_senha.php?token=" . urlencode($token);
    //$link = "http://localhost:3000/public/reset_senha.php?token=" . urlencode($token);

    $body = "
        <p>Olá <strong>" . htmlspecialchars($name) . "</strong>,</p>
        <p>Solicitaste a recuperação de senha. Clique no link abaixo para redefinir a tua senha:</p>
        <p><a href='$link'>$link</a></p>
        <p>Este link é válido por 1 hora.</p>
        <p>Se não solicitaste essa ação, podes ignorar este email.</p>
    ";

    return sendEmail($email, $name, 'Recuperação de Senha', $body, 'Suporte Comunidade');
}

function sendCommentVerificationEmail($email, $name, $token) {

    $link = getBaseUrl() . "/includes/verificar_comentario.php?token=" . urlencode($token);
    //$link = "http://localhost:3000/includes/verificar_comentario.php?token=" . urlencode($token);

    $body = "
        Olá <strong>$name</strong>,<br><br>
        Recebemos o teu comentário. Para que ele seja publicado, por favor confirma clicando no link abaixo:<br><br>
        <a href='$link'>$link</a><br><br>
        Obrigado por participares!
    ";

    return sendEmail($email, $name, 'Confirmação de Comentário', $body, 'Moderação de Comentários');
}

function getBaseUrl() {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:3000';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    return $protocol . '://' . $host;
}

?>
