<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include library PHPMailer
include('assets/PHPMailer/src/Exception.php');
include('assets/PHPMailer/src/PHPMailer.php');
include('assets/PHPMailer/src/SMTP.php');

// $id = $_POST['iduser'];
// $name = $_POST['nama'];
// $email = $_POST['email'];
// $stat = $_POST['status'];
// $pass = $_POST['password'];

// $query = "INSERT INTO users VALUES ('$id','$name','$email','$stat','$pass')";

// if( $query ){
    $email_pengirim = 'savelinks.mailer@gmail.com';
    $nama_pengirim = 'Savelinks Website';
    $email_penerima = "popon.pon543@gmail.com";

    $subjek = "Registration new user Savelinks.";
    $pesan = "Selamat akun anda berhasil ditambahkan";

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->Username = "savelinks.mailer@gmail.com";
    $mail->Password = "mznggtckqtktnjne";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPDebug = 2;

    $mail->setFrom('savelinks.mailer@gmail.com', 'Bedul');
    $mail->addAddress('popon.pon543@gmail.com');     //Add a recipient
    $mail->isHTML(true);
    $mail->Subject = $subjek;
    $mail->Body = $pesan;

    $send = $mail->send();

    if( $send ){
        echo "Email berhasil dikirim";
    } else {
        echo "Email gagal dikirim";
    }
// }
?>