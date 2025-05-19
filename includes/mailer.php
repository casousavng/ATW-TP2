<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../public/vendor/autoload.php';

function sendVerificationEmail($email, $name, $token) {
    $mail = new PHPMailer(true);

    try {
        // Configurações SMTP Mailtrap
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '9e3bc57eccee90'; // Teu Mailtrap username
        $mail->Password   = 'b3633bc59c5163'; // Teu Mailtrap password
        $mail->Port       = 2525;

        $mail->setFrom('no-reply@teusite.com', 'Registo Conta');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Confirme o seu registo';

        // ATUALIZA este link conforme a tua estrutura real
        $verificationLink = "http://localhost:3000/includes/verify.php?token=" . urlencode($token);

        $mail->Body = "
            Se bem-vindo a nossa comunidade, <strong>$name</strong>.<br><br>
            Por favor confirma o teu registo clicando no link abaixo:<br>
            <a href='$verificationLink'>$verificationLink</a><br><br>
            Obrigado!
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log ou echo $mail->ErrorInfo para debugging, se quiseres
        return false;
    }
}