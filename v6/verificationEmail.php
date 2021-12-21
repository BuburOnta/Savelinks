<?php 
session_start();
require "function.php";

// --- JIKA REGISTRASI TERLEBIH DAHULU MAKA WEBSITE TIDAK BISA DIAKSES ---
if( !$_SESSION['tempUser'] || !$_SESSION['tempPass'] || !$_SESSION['tempEmail'] ){
    header("Location: MAINregister.php");
    exit;
}

$error = []; // set varibael error menjadi array
$values = []; // set variabel values menjadi array
$errorKeys = ['verifCode']; // membuat error key
//$optional = ['confirm_password']; // optional untuk confirm pass

// Mengecek tombol regist
if( isset($_POST['verification'])) {
    // validasi form
    foreach( $errorKeys as $errorKey) { // mengeluarkan semua array
        // menggunakan error key dengan post untuk mengecek apakah input kosong atau tidak, jika kosong maka variabel error diisi dengan masing masing error key
        if( empty(trim($_POST[$errorKey]))){
            $error[] = $errorKey; // memasukkan key kedalam var error
        } else {
            $values[$errorKey] = $_POST[$errorKey];
        }
    }

    // Menghitung jumlah error key didalam variabel error, jika sudah 0 atau tidak ada error baru jalankan function register
    if(count($error) == 0) {
        // menerima data dari function register yang dimana jika dikembalikan nilai true (1), dan false (0)
        if( verification($_POST) > 0) { // mengirim value didalam $_POST ke function register
            // header("Location: verifikasiEmail.php");
            header("Location: MAINlogin.php");
        }else{
            echo mysqli_error($con);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Page</title>
    <style>@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans&family=Maven+Pro&family=Montserrat&family=Nunito+Sans&family=Quicksand&family=Rubik+Mono+One&display=swap');</style>
    <link rel="stylesheet" href="css/login-register.css">
</head>
<body>
    <nav>
        <h1 style="font-size: 30px;">Savelinks.</h1>
        <span style="background-color: black; width: 100%; height: 2px; display: block;"></span>
    </nav>


    <?php //if(isset($_POST['succes'])): ?>
        <!-- <p style="color: green; font-style:italic;font-size:15px;"><?php //echo $_POST['succes'] ?></p> -->
        <!-- Animasi loading -->
        <!-- <div class="col-3">
          <div class="snippet" data-title=".dot-falling">
            <div class="stage">
              <div class="dot-falling"></div>
            </div>
          </div>
        </div> -->

        <!-- <script>
            setTimeout(() => {
                document.location.href = "MAINlogin.php";
            }, 2000);
        </script> -->
    <?php //endif; ?>
    <div class="container">
        <main>
                <div class="row">
                    <div class="leftSide" style="text-align:center;">
                        <img src="img/logo-homepage.png" width="250" style="margin-bottom: 10px;">
                        <h1>Savelinks Project</h1>
                        <p>website ini bertujuan untuk menyimpan url sehingga dapat memudahkan anda untuk membuka link yang diinginkan tanpa harus membuat url baru</p>
                    </div>

                    <div class="rightSide">
                        <form method="POST" action="" class="registForm" autocomplete="off">

                            <!-- Menampilkan pesan error -->
                            <?php if(isset($_POST['error'])): ?>
                                <p style="color: red;font-style:italic;font-size:15px;"><?= $_POST['error'] ?></p>
                            <?php endif; ?>


                                <!-- CODE OTP -->
                                <div class="regist-group username">
                                    <label for="verifCode">Kode verifikasi</label><?//= $error['verifCode'] ?><br>
                                    <input type="text" name="verifCode" id="verifCode">
                                    <?php if (in_array('verifCode', $error)): ?>
                                        <span class="error">Kode verifikasi tidak boleh kosong</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Submit -->
                                <div class="regist-group daftar">
                                    <button type="submit" name="verification">verifikasi</button>
                                </div>

                            </form>
                            <span class="foot">Tidak menerima Kode Verifikasi? <a href="MAINregister.php">Kirim ulang</a></span>

                    </div>
                </div>
        </main>

    </div>
</body>
<style>
.error {
    color: red;
    font-size: 12px;
    font-style: italic;
}
</style>
</html>