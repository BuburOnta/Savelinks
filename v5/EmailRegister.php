<?php
require "function.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Include library PHPMailer
include('asset/PHPMailer/src/Exception.php');
include('asset/PHPMailer/src/PHPMailer.php');
include('asset/PHPMailer/src/SMTP.php');


function register($data)
{ //menangkap value $_POST dengan variabel $data
    global $con;
    // VARIABLE 1 - membersihkan blackslash dan mengubah menjadi huruf kecil semua
    $username = strtolower(stripslashes($data["username"]));
    // VARIABLE 2 - memungkinkan user memasukan password dengan tanda kutip
    $password = mysqli_real_escape_string($con, $data["password"]);
    $confirmPass = mysqli_real_escape_string($con, $data["confirm_password"]);
    $email = mysqli_real_escape_string($con, $data["email"]);
    // VARIABLE 2 - mefilter html
    $username = htmlspecialchars($username);
    $password = htmlspecialchars($password);
    $confirmPass = htmlspecialchars($confirmPass);
    $email = htmlspecialchars($email);

    // KONDISI 1 - Cek username sudah terpakai atau belum
    $result = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
    if (mysqli_fetch_assoc($result)) { // mengeluarkan data dan memberi kondisi 1
        $_POST['error'] = "username tidak tersedia!";
        return false; // mengembalikan nilai false kepada else di register php
    }

    // KONDISI 2 - Jika user tersedia maka cek ketersediaan email
    $result = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
    if (mysqli_fetch_assoc($result)) {
        $_POST['error'] = "Email sudah terdaftar!";
        return false; // mengembalikan nilai false kepada else di register php
    }

    // KONDISI 3 - Jika KONDISI 2 berhasil maka Lakukan cek konfirmasi password
    if ($password !== $confirmPass) {
        $_POST['errorPass'] = "Konfirmasi Password tidak sesuai";
        return false;
    }

    // KONDISI 4 - Memverifikasi email dengan cara mengirimkan kode otp
    $code_otp = rand(999999, 111111);
    $mail = new PHPMailer();
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Username = 'popon.pon321@gmail.com';
    $mail->Password = 'dlbnxsdkwvrcftlo';
    $mail->setFrom('popon.pon321@gmail.com', 'Savelinks');
    $mail->addAddress($email);
    $mail->Subject = 'Kode OTP Verifikasi Email Savelinks.';
    $mail->Body = 'Kode verifikasi anda: ' . $code_otp;

    if (!$mail->send()) {
        // echo 'Mailer Error: ' . $mail->ErrorInfo;
        echo "ERROR CODE OTP";
        return false;
    }

    // KONDISI 5 - Jika di tempuser sudah ada username yg sama maka hanya menguba code_otp
    $result = mysqli_query($con, "SELECT email FROM temp_users WHERE email='$email'");
    if (mysqli_fetch_assoc($result)) {
        mysqli_query($con, "UPDATE temp_users SET code_otp=$code_otp WHERE email='$email'");
        $_SESSION['tempUser'] = $username;
        $_SESSION['tempPass'] = $password;
        $_SESSION['tempEmail'] = $email;
        return mysqli_affected_rows($con); // mengembalikan nilai false kepada else di register php
    }

    // Jika Kondisi 1 & 2 & 3 berhasil maka
    // Enkripsi password lalu menambahkan user ke database user ssementara
    $password = password_hash($password, PASSWORD_DEFAULT);
    mysqli_query($con, "INSERT INTO temp_users SET username='$username', password='$password', email='$email', code_otp=$code_otp, status='not verified' ");

    // Jika semua query berhasil mengembalikan nilai true
    $_POST['succes'] = "Registrasi berhasil!";
    $_SESSION['tempUser'] = $username;
    $_SESSION['tempPass'] = $password;
    $_SESSION['tempEmail'] = $email;

    return mysqli_affected_rows($con);
}


function verification($data)
{
    global $con;
    $code_otp = $data['verifCode'];
    $username = $_SESSION['tempUser'];
    $password = password_hash($_SESSION['tempPass'], PASSWORD_DEFAULT);
    $email = $_SESSION['tempEmail'];

    $temp_user = mysqli_query($con, "SELECT * FROM temp_users WHERE username='$username'");
    $r = mysqli_fetch_assoc($temp_user);

    // KONDISI 1 - Menyamakan input code oTP dengan di database
    if ($code_otp == $r['code_otp']) {
        mysqli_query($con, "UPDATE temp_users SET code_otp='' WHERE username='$username'"); // mengembalikan nilai code otp menjadi kosong kembali
        mysqli_query($con, "INSERT INTO users SET username='$username', password='$password', email='$email' ");
        $_SESSION = [];
        return mysqli_affected_rows($con);
    } else {
        $_POST['error'] = "Kode verifikasi salah!";
        return false;
    }
}
