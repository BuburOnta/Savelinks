<?php 
require 'assets/PHPMailerAutoload.php';
$mail = new PHPMailer;
$username = 'savelinks.mailer@gmail.com';
$password = 'savelinks000';


$mail->SMTPDebug = 2;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = $username;                 // SMTP username
$mail->Password = $password;                           // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 582;                                    // TCP port to connect to

$mail->setFrom($username);
$mail->addAddress('popon.pon543@gmail.com');     // Add a recipient

// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Savelinks OTP';
$mail->Body    = '12345';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>