<?php 
session_start();
require "function.php";
// Cek session
if( !$_SESSION['sesiLogin'] && !$_SESSION['sesiId'] ){
    header("Location: MAINlogin.php");
}

$id = $_SESSION['sesiId'];
$links = query("SELECT * FROM links WHERE id_user=$id");
$users = query("SELECT * FROM users WHERE id=$id")[0];


// UPDATE
if(isset($_POST['update'])) {
    if(updateProfile($_POST) > 0 ){
        header("Location: PAGEprofile.php");
    } else {
        header("Location: error.php");
    }
}


// LOGOUT
if(isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header("Location: MAINlogin.php");
}

?>
<html lang="id">
<head>
    <title>Index</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
</head>
<body>
    <div class="container blur">
    <header>
        <a href="index.php">Savelinks.</a>
        <a href="PAGEprofile.php" class="profile"></a>
    </header>


    <main>
            <div class="row">
                <!-- Form -->
                <div class="addlink">
                    <h2>Add Link</h2>
                    <form method="POST" action="" class="addLink" autocomplete="off">
                        <!-- Link Title -->
                        <div class="form-group link-title">
                            <label for="link-title">Link Title</label><br>
                            <input type="text" name="link-title" id="link-title" placeholder="Enter a title...">
                            <span class="garis"></span>
                        </div>

                        <!-- URL -->
                        <div class="form-group link-url">
                            <label for="link-url">URL</label><br>
                            <input type="url" name="link-url" id="link-url" placeholder="https://...">
                            <span class="garis"></span>
                        </div>

                        <!-- Submit -->
                        <div class="link-add">
                            <button type="submit" name="link-add" id="link-add">+ Add To Link List</button>
                        </div>
                    </form>
                </div>

                <!-- List Link -->
                <div class="listLink">
                    <h2>Link List</h2>
                    <div class="position">
                        <!-- Search Title -->
                        <form method="POST" action="" class="search-title">
                            <div class="form-group search-title">
                                <label for="search-title">Search Title</label><br>
                                <input type="text" name="search-title" id="search-title" placeholder="Enter Keyword..." autocomplete="off">
                                <button type="submit" name="search" hidden>Search</button>
                                <span class="garis"></span>
                            </div>
                        </form>

                        <!-- List -->
                        <div class="list-group">
                            <label>List</label><br>
                            <ol type="1" class="list-num">
                            <!-- Pengulangan -->
                            <?php foreach($links as $row) : ?>
                                <div class="list-link">
                                    <!-- <div class="break" onclick="showAction()">
                                        <a href="PAGE-PRSedit.php?idlink=<?= $row['id_link']; ?>" class="edit">Edit</a>
                                        <a href="PAGE-PRSdelete.php?idlink=<?= $row['id_link']; ?>" class="delete" onclick="return confirm('yakin ingin menghapus?')">delete</a>
                                    </div> -->
                                    <li><?= $row['title'] ?></li>
                                    <ul>
                                        <li><a href="<?= $row['url'] ?>" target="_blank"><?= $row['url'] ?></a></li>
                                    </ul>
                                </div>

                            <?php endforeach;?>
                            </ol>
                        </div>

                    </div>
                </div>

            </div>
    </main>
    </div>

    <!-- profile -->
    <div class="profile">
        <h2>Profile</h2>
        <form method="POST" action="" class="profileForm">
            <input type="hidden" name="id" value="<?=$users['id']?>">
            <!-- Nama -->
            <div class="form-group nama">
                <label for="name">Name</label><br>
                <input type="text" name="name" id="name" value="<?= $users['name'] ?>">
                <span class="garis"></span>
            </div>

            <!-- Username -->
            <div class="form-group username">
                <label for="username">Username</label><br>
                <input type="text" name="username" id="username" value="<?= $users['username'] ?>" readonly>
                <span class="garis"></span>
            </div>

            <!-- Email -->
            <div class="form-group email">
                <label for="email">Email</label><br>
                <input type="email" name="email" id="email" value="<?= $users['email'] ?>" readonly>
                <span class="garis"></span>
            </div>

            <!-- Password -->
            <div class="form-group password">
                <!-- <label for="password">Password</label><br> -->
                <input type="hidden" name="password" id="password" value="<?= $users['password'] ?>">
                <span class="garis"></span>
            </div>

            <!-- New Password -->
            <div class="form-group new-pass">
                <div class="new">
                    <label for="new-pass">New Password</label><br>
                    <input type="password" name="new-pass" id="new-pass" placeholder="your New Password">
                    <div class="garis1"></div>
                </div>

                <div class="confirm">
                    <label for="confirm-new-pass">Confirm New Password</label><br>
                    <input type="password" name="confirm-new-pass" id="confirm-new-pass" placeholder="your New Password">
                    <div class="garis2"></div>
                </div>
            </div>

            <!-- Logout -->
            <div class="button">
                <button class="change-data" name="update" type="submit">Update Data</button>
            </div>
        </form>
        <button class="logout" name="logout"><a href="PAGEprofile.php?logout" style="text-decoration: none; color:red;">Log Out</a></button>
        <button class="logout" name="goback"><a href="index.php" style="text-decoration: none; color:white;">Kembali</a></button>
    </div>
</body>
</html>