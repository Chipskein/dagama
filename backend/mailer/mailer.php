
<?php
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
function send_mail($addr_mail,$subject,$html){
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->SMTPAuth = true;
    $mail->AuthType = 'XOAUTH2';
    $email = "dagama.ifrs@gmail.com";
    $clientId = '971777937005-dqnkd7sef0410teq7etqnl5es07ocha5.apps.googleusercontent.com';
    $clientSecret = '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';
    $refreshToken = '1//0h8NFG9y7CvcTCgYIARAAGBESNwF-L9Ir7YVXsCFyOGedjirP9o5GdAanrM21VsBzA-4FS2pbjMsBHfXE6eaR20R3wLwpDeR2B9Q';
    $provider = new Google(
        [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
        ]
    );
    $mail->setOAuth(
        new OAuth(
            [
                'provider' => $provider,
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'refreshToken' => $refreshToken,
                'userName' => $email,
            ]
        )
    );
    $mail->setFrom($email);
    $mail->addAddress($addr_mail);
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $html;
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Mensagem Enviada';
    }
}
$teste=file_get_contents("pages/page1.html");
$email_to_send="abfn0905@gmail.com";
send_mail("$email_to_send","EMAIL DE TESTE","$teste");


 
