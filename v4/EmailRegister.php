<?php 
require "function.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include library PHPMailer
include('assets/PHPMailer/src/Exception.php');
include('assets/PHPMailer/src/PHPMailer.php');
include('assets/PHPMailer/src/SMTP.php');


function register($data) { //menangkap value $_POST dengan variabel $data
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
        if(mysqli_fetch_assoc($result)) { // mengeluarkan data dan memberi kondisi 1
            $_POST['error'] = "username tidak tersedia!";
            return false; // mengembalikan nilai false kepada else di register php
        }

        // KONDISI 2 - Jika user tersedia maka cek email
        $result = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
        if(mysqli_fetch_assoc($result)) {
            $_POST['error'] = "email sudah pernah didaftarkan";
            return false; // mengembalikan nilai false kepada else di register php
        }

        // KONDISI 3 - Jika KONDISI 2 berhasil maka Lakukan cek konfirmasi password
        if($password !== $confirmPass) {
            $_POST['errorPass'] = "Konfirmasi Password tidak sesuai";
            return false;
        }

        // Jika Kondisi 1 & 2 & 3 berhasil maka 
        // Enkripsi password lalu menambahkan user
        $password = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($con, "INSERT INTO users SET username='$username', password='$password', email='$email' ");

        // Jika semua query berhasil mengembalikan nilai true
        $_POST['succes'] = "Registrasi berhasil!";
        return mysqli_affected_rows($con);
    }
?>