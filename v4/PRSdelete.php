<?php 
session_start();
require "function.php";

// mengambil id dari url
$id = $_GET["id"];

// QUery Delete
$result = mysqli_query($con, "DELETE FROM users WHERE id=$id") or die("<script>alert('Delete Gagal!');document.location.href='MAINadmin.php';</script>;");
header("Location: MAINadmin.php");
?>