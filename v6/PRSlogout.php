<?php 
require "function.php";

session_start();
$_SESSION = [];
session_unset();
session_destroy();
header("Location: MAINlogin.php");

// COOKIE logout
$id = $_COOKIE['id']; // mengambil id
mysqli_query($con, "UPDATE users SET cookie='' WHERE id=$id"); // mengubah cookie di database menjadi kosong
setcookie('id', '', time()-3600);
exit;
?>