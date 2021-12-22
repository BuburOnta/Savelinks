<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Include library PHPMailer
include('asset/PHPMailer/src/Exception.php');
include('asset/PHPMailer/src/PHPMailer.php');
include('asset/PHPMailer/src/SMTP.php');

// DEFINE VAR
define("USERNAME_EMAIL", "popon.pon321@gmail.com");
define("PASSWORD_EMAIL", "dlbnxsdkwvrcftlo");
// define("PASSWORD_EMAIL", "qwagzwmzxgdajdbq");

// koneksi
$con = mysqli_connect('localhost', 'root', '', 'savelinks');
if (!$con) {
    die("Koneksi Gagal");
}

$error = "";

// --- Main Query ---
function query($query)
{
    global $con;
    $result = mysqli_query($con, $query) or die('Gagal menampilkan data');;
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}


/* --- USER QUERY --- */
// --- REGISTER --- //
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
        return false;
    }

    // KONDISI 2 - Jika user tersedia maka cek ketersediaan email
    $result = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
    if (mysqli_fetch_assoc($result)) {
        $_POST['error'] = "Email sudah terdaftar!";
        return false;
    }

    // KONDISI 3 - Jika KONDISI 2 berhasil maka Lakukan cek konfirmasi password
    if ($password !== $confirmPass) {
        $_POST['errorPass'] = "Konfirmasi Password tidak sesuai";
        return false;
    }

    // KONDISI 4 - Memverifikasi email dengan cara mengirimkan kode otp
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
    $mail->setFrom(USERNAME_EMAIL, 'Savelinks');
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
// - VERIFIKASI EMAIL ---
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
        mysqli_query($con, "UPDATE temp_users SET code_otp='', status='verified' WHERE username='$username'"); // mengembalikan nilai code otp menjadi kosong kembali
        mysqli_query($con, "INSERT INTO users SET username='$username', password='$password', email='$email' ");
        $_SESSION = [];
        return mysqli_affected_rows($con);
    } else {
        $_POST['error'] = "Kode verifikasi salah!";
        return false;
    }
}


// --- Login ---
function login($data)
{
    global $con;

    // VARIABEL 1
    $username = $data['username'];
    $password = $data['password'];

    // QUERY 1 - Mengecek kesamaan username di database dgn input beserta password
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($con, $query);

    // KONDISI 1 - Jika username dan password ada di database
    if (mysqli_num_rows($result) === 1) {
        // KONDISI 2 - Mengecek Password
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            // SESSION - Membuat 1 session dengan key sesiLogin
            $_SESSION['sesiLogin'] = $username;
            $_SESSION['sesiId'] = $row['id'];

            // COOKIE - Membuat fitur remember me
            if (isset($data['remember'])) {
                // Memasukan cookie ke database
                $id = $row['id'];
                $key = hash('sha256', $row['username']); //variabel $key dengan isi username yang diacak
                mysqli_query($con, "UPDATE users SET cookie='$key' WHERE id=$id");
                setcookie('id', $id, time() + 7 * 24 * 60 * 60); // mengirim id ke key id // set cookie selama 7 hari
            }

            return true;
        } else {
            $_POST['error'] = "Invalid Username Or Password";
            return false;
        }
    } else {
        $_POST['error'] = "Invalid Username Or Password";
        return false;
    }
    //} else {
    //    return false;
    //}
}


// --- FORGET PASSWORD --- //
function forget($data)
{
    global $con;
    $email = mysqli_real_escape_string($con, $data["email"]);
    $email = htmlspecialchars($email);


    // KONDISI 1 - Mengecek email apakah terdaftar didalam database
    $result = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
    if (!mysqli_fetch_assoc($result)) {
        $_POST['error'] = "Email tidak terdaftar!";
        return false;
    }

    // KONDISI 2 - Mengirim kode otp kepada email yang ditulis
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
    $mail->setFrom(USERNAME_EMAIL, 'Savelinks');
    $mail->addAddress($email);
    $mail->Subject = 'Kode OTP Lupa Password Savelinks.';
    $mail->Body = 'Kode lupa password anda: ' . $code_otp;

    if (!$mail->send()) {
        // echo 'Mailer Error: ' . $mail->ErrorInfo;
        echo "ERROR CODE OTP";
        return false;
    }

    // KONDISI 5 - Jika di tempuser sudah ada username yg sama maka hanya menguba code_otp
    $result = mysqli_query($con, "SELECT email FROM temp_users WHERE email='$email'");
    if (mysqli_fetch_assoc($result)) {
        mysqli_query($con, "UPDATE temp_users SET code_otp=$code_otp WHERE email='$email'");
        $_SESSION['email'] = $email;
        return mysqli_affected_rows($con); // mengembalikan nilai false kepada else di register php
    }

    $_SESSION['email'] = $email;
    return true;
}

// Forget Verification
function forgetVerif($data)
{
    global $con;
    $code_otp = $data['verifCode'];
    $email = $_SESSION['email'];

    $temp_user = mysqli_query($con, "SELECT * FROM temp_users WHERE email='$email'");
    $r = mysqli_fetch_assoc($temp_user);

    // KONDISI 1 - Menyamakan input code oTP dengan di database
    if ($code_otp == $r['code_otp']) {
        mysqli_query($con, "UPDATE temp_users SET code_otp='' WHERE email='$email'"); // mengembalikan nilai code otp menjadi kosong kembali
        return mysqli_affected_rows($con);
    } else {
        $_POST['error'] = "Kode verifikasi salah!";
        return false;
    }
}

// UBAH PASSWORD NYA
function changePass($data)
{
    global $con;
    $email = $_SESSION['email'];
    $newPass = $_POST['new-pass'];
    $confirmNewPass = $_POST['confirm-new-pass'];

    if ($newPass !== $confirmNewPass) {
        $_POST['error'] = "Konfirmasi Password tidak sesuai";
        return false;
    }
    $password = password_hash($newPass, PASSWORD_DEFAULT);
    mysqli_query($con, "UPDATE users SET password='$password' WHERE email='$email'");
    $_SESSION['email'] = '';
    return mysqli_affected_rows($con);
}



// --- Update Profile ---
function updateProfile($data)
{
    global $con;
    // Mengambil data menjadi variabel
    $id = $data["id"];
    $name = htmlspecialchars($data['name']);
    $username = htmlspecialchars($data['username']);
    $email = htmlspecialchars($data['email']);
    $passwordLama = ($data['password']);


    // Jika user mengganti password
    if ($_POST['new-pass'] == "") { // mengecek apakah key new-pass kosong atau tidak
        $password = $passwordLama; // jika kosong maka variabel password diisi dngn password lama
    } else {
        $password = newPass(); // jika ada isinya maka variabel password diisi dngn function newPass
    }

    // query insert data
    $query = "UPDATE users SET name='$name', username='$username', password='$password', email='$email' WHERE id=$id ";
    if (!mysqli_query($con, $query)) {
        return false;
    }
    $_SESSION['sesiLogin'] = $username;
    return mysqli_affected_rows($con);
}

// --- NEW PASS ---
function newPass()
{
    // variabel
    $newPass = $_POST['new-pass'];
    $confirmNewPass = $_POST['confirm-new-pass'];
    $passwordLama = $_POST["password"];
    // var_dump($passwordLama);die;

    // mengecek kesamaan password
    if ($newPass !== $confirmNewPass) {
        echo "<script>
        alert('Invalid Confirm Pass');
        </script>";
        return $passwordLama; // jika pasword salah maka diisi dengan password lama
    }

    // enkripsi password
    $newPass = password_hash($newPass, PASSWORD_DEFAULT);
    return $newPass; // jika berhasil maka mengembalikan nilai new password
}



// --- Add Link ---
function addlink($data)
{
    global $con;

    // VARIABEL 1
    $linkTitle = $data['link-title'];
    $linkUrl = $data['link-url'];
    $idUser = $_SESSION['sesiId'];

    // KONDISI - Mengecek apakah form kosong
    if ($linkTitle != "" && $linkUrl != "") {
        // insert data
        $query = "INSERT INTO links SET id_user=$idUser, title='$linkTitle', url='$linkUrl' ";
        $result = mysqli_query($con, $query);
        return mysqli_affected_rows($con);
    } else {
        $_POST['error'] = "!!!!";
        return false;
    }
}


// --- Update Link ---
function updateLink($data)
{
    global $con;
    $linkTitle = $data['link-title'];
    $linkUrl = $data['link-url'];
    $idUser = $_SESSION['sesiId'];
    $idLink = $data['id-link'];

    // KONDISI - Mengecek apakah form kosong
    if ($linkTitle != "" && $linkUrl != "") {
        // Maka jalankan query update data
        $query = "UPDATE links SET title='$linkTitle', url='$linkUrl' WHERE id_link=$idLink";
        $result = mysqli_query($con, $query);
        return mysqli_affected_rows($con);
    } else {
        $_POST['error'] = "!!!!";
        return false;
    }
}



// --- Search ---
function search($keyword)
{
    global $con;
    $idUser = $_SESSION['sesiId'];
    $query = "SELECT * FROM links WHERE id_user=$idUser and title LIKE '%$keyword%'";

    return query($query);
}







/* --- ADMIN PAGE --- */
// --- Update ---
function update($data)
{
    global $con;
    // membuat variabel
    // mencegah adanya element html
    $id = $data["id"];
    $username = htmlspecialchars($data['username']);
    $password = htmlspecialchars($data['password']);
    $password = password_hash($password, PASSWORD_DEFAULT); // enkripsi password
    $email = htmlspecialchars($data['email']);

    // query insert data
    $query = "UPDATE users SET username='$username', password='$password', email='$email' WHERE id=$id ";
    mysqli_query($con, $query);
    // mengembalikan nilai angka dari query
    // kalo berhasil maka '1' klo gagal maka '-1'
    return mysqli_affected_rows($con);
}
