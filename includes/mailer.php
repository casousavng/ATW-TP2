<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../public/vendor/autoload.php';

function configureMailer(): PHPMailer {
    $mail = new PHPMailer(true);

    // Configurações SMTP
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = '9e3bc57eccee90';
    $mail->Password   = 'b3633bc59c5163';
    $mail->Port       = 2525;

    // ⚠️ Codificação UTF-8 para suportar acentos
    $mail->CharSet = 'UTF-8';

    return $mail;
}

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
    $verificationLink = "http://localhost:3000/includes/verify.php?token=" . urlencode($token);

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
?>