<?php 
require 'function.php';
$keyword = $_GET['keyword'];

$links = query("SELECT * FROM links WHERE
                title LIKE '%$keyword%' OR
                url LIKE '%$keyword%'");
// var_dump($links);die;
?>
<!-- menampilkan Daata -->
<!-- List -->
<div class="list-group" id="list-group">
    <label>List</label><br>
    <a href="index.php?delete&idUs=<?= $links[0]['id_user'] ?>&clearAll" class="clearAll btn btn-success" onclick="return confirm('Apa anda yakin ingin menghapus semua tugas?')">Clear All</a>
    <ol type="1" class="list-num">
        <!-- Pengulangan -->
        <?php foreach ($links as $row) : ?>
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
                    <a href="index.php?delete&idLink=<?= $row['id_link']; ?>" class="delete"><i class="fas fa-trash"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </ol>
</div>