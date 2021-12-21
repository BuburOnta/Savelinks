<?php 
session_start();
require "function.php";
// Cek session
if( !$_SESSION['sesiLogin'] && !$_SESSION['sesiId'] ){
    header("Location: MAINlogin.php");
}

$id = $_SESSION['sesiId'];
$links = query("SELECT * FROM links WHERE id_user=$id");

// --- Search Title ---
if( isset($_POST['search'])) {
    $links = search($_POST['search-title']); // mengirim keyword
}

// --- Sort By ---
if( isset($_GET['desc'])){
    $links = query("SELECT * FROM links WHERE id_user=$id ORDER BY id_link DESC");
}

// --- Add Link ---
if( isset($_POST['link-add'])){

    if( addlink($_POST) > 0){
        header("Location: index.php");
    } else {
        echo mysqli_error($con);
    }
}

// --- Delete ---
if( isset($_GET['delete'])){

    // Clear All
    if( isset($_GET['idUs']) && isset($_GET['clearAll']) ){
        $idUser = $_GET['idUs'];
        if($result = mysqli_query($con, "DELETE FROM links WHERE id_user=$idUser")){
           header("Location: index.php");
        } else {
            header("Location: index.php?error");
        }

    } else if(isset($_GET['idLink'])){
        // Delete by 1
        $idLink = $_GET['idLink'];
        if( $result = mysqli_query($con, "DELETE FROM links WHERE id_link=$idLink")){
            header("Location: index.php");
        } else {
            header("Location: index.php?error");
        }
    }
}
?>
<html lang="id">
<head>
    <title>Index</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
</head>
<body>
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
                                <label for="search-title" class="sort-by">Search Title</label><br>
                                <!-- <input type="text" name="search-title" id="search-title" placeholder="Enter Keyword..." autofocus autocomplete="off"> -->
                                <input type="text" name="keyword" id="keyword" placeholder="masukkan keyword pencarian..." size="40" autocomplete="off">
                                <button type="submit" name="search" id="search" hidden>Search</button>
                                <span class="garis"></span>
                            </div>
                        </form>
                        <!-- Sort By -->
                        <form method="POST" action="" class="sort-by">
                            <div class="form-group sort-by">
                                <div class="checkGroup">
                                    Sort BY
                                    <input style="cursor: pointer;" type="checkbox" name="checkSort-by">
                                </div>
                                <ul class="listSort">
                                    <li><a href="index.php?desc">Terbaru</a></li>
                                    <li><a href="index.php">Terlama</a></li>
                                </ul>
                            </div>
                        </form>

                    <!-- List -->
                    <div class="list-group" id="list-group">
                        <label>List</label><br>
                        <a href="index.php?delete&idUs=<?= $links[0]['id_user'] ?>&clearAll" class="clearAll btn btn-success" onclick="return confirm('Apa anda yakin ingin menghapus semua tugas?')">Clear All</a>
                        <ol type="1" class="list-num">
                        <!-- Pengulangan -->
                        <?php foreach($links as $row) : ?>
                            <div class="flexing">
                                <div class="theleft">
                                    <div class="list-link">
                                        <li><?= $row['title'] ?></li>
                                        <ul>
                                            <li><a href="<?= $row['url'] ?>" target="_blank" style="color: blue;"><?= $row['url'] ?></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="break" onclick="showAction()">
                                    <a href="index.php?delete&idLink=<?= $row['id_link']; ?>" class="delete" ><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        <?php endforeach;?>
                        </ol>
                    </div>

                </div>
            </div>

        </div>
    </main>
</body>
<script src="js/script.js"></script>
</html>