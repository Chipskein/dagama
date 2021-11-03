
<?php
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
date_default_timezone_set('Etc/UTC');
function send_mail($addr_mail){
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
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
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message sent!';
    }
}




 
