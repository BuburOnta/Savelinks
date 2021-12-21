<?php 
session_start();
require "function.php";

$error = []; // set varibael error menjadi array
$values = []; // set variabel values menjadi array
$errorKeys = ['username', 'email', 'password','confirm_password']; // membuat error key
//$optional = ['confirm_password']; // optional untuk confirm pass

// Mengecek tombol regist
if( isset($_POST['register'])) {
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
        if( register($_POST) > 0) { // mengirim value didalam $_POST ke function register
            header("Location: verificationEmail.php");
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
    <title>Register Page</title>
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


                                <!-- Username -->
                                <div class="regist-group username">
                                    <label for="username">Username</label><?//= $error['username'] ?><br>
                                    <input type="text" name="username" id="username">
                                    <?php if (in_array('username', $error)): ?>
                                        <span class="error">Username tidak boleh kosong</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Email -->
                                <div class="regist-group email">
                                    <label for="email">Email</label><br>
                                    <input type="email" name="email" id="email">
                                    <?php if (in_array('email', $error)): ?>
                                        <span class="error">Email tidak boleh kosong</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Password -->
                                <div class="regist-group password">
                                    <label for="password">Password</label><br>
                                    <input type="password" name="password" id="password">
                                    <?php if (in_array('password', $error)): ?>
                                        <span class="error">Password tidak boleh kosong</span>
                                    <?php endif; ?>
                                </div>
                                <!-- Confirm Password -->
                                <div class="regist-group confirm_password">
                                    <label for="confirm_password">Confirm Password</label><br>
                                    <input type="password" name="confirm_password" id="confirm_password">
                                    <!-- Error saat input kosong -->
                                    <?php if(in_array('confirm_password', $error)): ?>
                                        <span class="error">Confirm Password tidak boleh kosong</span>
                                    <?php endif; ?>
                                    <!-- Error saat konfirmasi password tidak sesuai -->
                                    <?php if (isset($_POST['errorPass'])): ?>
                                        <span class="error">Confirm Password tidak Sesuai</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Daftar -->
                                <div class="regist-group daftar">
                                    <button type="submit" name="register">Daftar</button>
                                </div>

                            </form>
                            <span class="foot">Sudah Punya Akun? <a href="MAINlogin.php">Login</a></span>

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


/**
 * ==============================================
 * Dot Falling
 * ==============================================
 */

.stage {
    position: absolute;
    top: 50%;left: 50%;
    transform: translate(-50%,-50%);
}
.dot-falling {
  position: relative;
  left: -9999px;
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: #9880ff;
  color: #9880ff;
  box-shadow: 9999px 0 0 0 #9880ff;
  animation: dotFalling 1s infinite linear;
  animation-delay: .1s;
}

.dot-falling::before, .dot-falling::after {
  content: '';
  display: inline-block;
  position: absolute;
  top: 0;
}

.dot-falling::before {
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: #9880ff;
  color: #9880ff;
  animation: dotFallingBefore 1s infinite linear;
  animation-delay: 0s;
}

.dot-falling::after {
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: #9880ff;
  color: #9880ff;
  animation: dotFallingAfter 1s infinite linear;
  animation-delay: .2s;
}

@keyframes dotFalling {
  0% {
    box-shadow: 9999px -15px 0 0 rgba(152, 128, 255, 0);
  }
  25%,
  50%,
  75% {
    box-shadow: 9999px 0 0 0 #9880ff;
  }
  100% {
    box-shadow: 9999px 15px 0 0 rgba(152, 128, 255, 0);
  }
}

@keyframes dotFallingBefore {
  0% {
    box-shadow: 9984px -15px 0 0 rgba(152, 128, 255, 0);
  }
  25%,
  50%,
  75% {
    box-shadow: 9984px 0 0 0 #9880ff;
  }
  100% {
    box-shadow: 9984px 15px 0 0 rgba(152, 128, 255, 0);
  }
}

@keyframes dotFallingAfter {
  0% {
    box-shadow: 10014px -15px 0 0 rgba(152, 128, 255, 0);
  }
  25%,
  50%,
  75% {
    box-shadow: 10014px 0 0 0 #9880ff;
  }
  100% {
    box-shadow: 10014px 15px 0 0 rgba(152, 128, 255, 0);
  }
}
</style>
</html>