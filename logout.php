<?php 
session_start();
session_destroy();
header('Location: index.php');
die();
#echo "<script>window.location='index.php';</script>";
?>