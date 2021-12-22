<?php
// require "function.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Include library PHPMailer
include('asset/PHPMailer/src/Exception.php');
include('asset/PHPMailer/src/PHPMailer.php');
include('asset/PHPMailer/src/SMTP.php');
// DEFINE VAR
define("USERNAME_EMAIL", "popon.pon321@gmail.com");
define("PASSWORD_EMAIL", "dlbnxsdkwvrcftlo");
$code_otp = rand(999999, 111111);
$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPDebug = SMTP::DEBUG_OFF;
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->SMTPAuth = true;
$mail->Username = USERNAME_EMAIL;
$mail->Password = PASSWORD_EMAIL;
$mail->setFrom(USERNAME_EMAIL, 'Savelinker');
$mail->addAddress('lah.ontaks@gmail.com');
$mail->Subject = 'Kode OTP Verifikasi Email Savelinks.';
$mail->Body = $code_otp;

if (!$mail->send()) {
    // echo 'Mailer Error: ' . $mail->ErrorInfo;
    echo "ERROR CODE OTP";
    die;
}
echo $code_otp;
?>

<?php 

// var_dump(USERNAME_EMAIL);
// echo '<br>';
// var_dump(PASSWORD_EMAIL);

?>