<?php
require 'function.php';
// Ambil data di URL
$id = $_GET["id"];  

// QUERY 1 - Mengambil data mahasiswa berdasarkan id
$users = query("SELECT * FROM users WHERE id=$id")[0]; //menggunakan function yg sudah ada, menghasilkan array numerik

// membuat logic apakah tombol submit sudah dipencet atau blm
if (isset($_POST['submit'])) {
    var_dump($_POST);
    // cek apakah data berhasil diubah atau tidak
    if( update($_POST) > 0){
        echo "
        <script>
            alert('data berhasil diubah');
            document.location.href = 'MAINadmin.php';
        </script>
        ";
    }else {
        echo "
        <script>
            alert('data gagal diubah');
        </script>
        ";
    }
}



?>

<html>
<head>
    <title>Ubah Data</title>
</head>
<body>
    <h1>Ubah Data Hewan</h1>
<br>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?=$users['id'] ?>">
        <ul>
            <li>
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?=$users['username'] ?>">
            </li>

            <li>
                <label for="password">Password</label>
                <input type="text" name="password" id="password" value="<?=$users['password'] ?>">
            </li>

            <li>
                <label for="email">Email</label>
                <input type="text" name="email" id="email" value="<?=$users['email'] ?>">
            </li>
            <li>
                <button type="submit" name="submit">Ubah</button>
            </li>
        </ul>
    </form>
</body>
</html>