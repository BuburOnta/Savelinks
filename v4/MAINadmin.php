<?php 
require "function.php";
$users = query("SELECT * FROM users");
// var_dump($users);

// ketika tombol urutan tekan
if(isset($_POST["urut_berdasarkan"])) {
    $urutan = $_POST["urutan"];
    if( $urutan=="terbaru" ) {
        $users = query("SELECT * FROM users ORDER BY id DESC");
    } else if( $urutan=="terlama"){
        $users = query("SELECT * FROM users ORDER BY id ASC");
    }
}
?>
<html lang="en">
<head>
    <title>Admin Page</title>
</head>
<body>

<div class="container">
    <h1>List Akun Yang Terdaftar</h1>

    <!-- Filter -->
    <form method="POST" action="">
        <label for="urutan">Urut berdasarkan</label><br>
        <select name="urutan" id="urutan">
            <option value="" hidden></option>
            <option value="terbaru">Terbaru</option>
            <option value="terlama">Terlama</option>
        </select>
        <button type="submit" name="urut_berdasarkan">Reload</button>
    </form>
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <th>No</th>
            <th>ID</th>
            <th>Name</th>
            <th>Username</th>
            <th>Password</th>
            <th>Email</th>
            <th>Cookie</th>
            <th>Aksi</th>
        </thead>
<?php $i = 1; ?>
<?php foreach($users as $user){ ?>
        <tr>
            <td><?= $i; ?></td>
            <td><?= $user["id"]; ?></td>
            <td><?= $user["name"]; ?></td>
            <td><?= $user["username"]; ?></td>
            <td><?= $user["password"]; ?></td>
            <td><?= $user["email"]; ?></td>
            <td><?= $user["cookie"]; ?></td>
            <td>
                <a href="PRSupdate.php?id=<?= $user["id"] ?>">Edit</a> | 
                <a href="PRSdelete.php?id=<?= $user["id"] ?>" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
            </td>
        </tr>
<?php $i++; }; ?>
    </table>
</div>

</body>
</html>