<?php 
session_start();
$_SESSION['isadmin'] = false;
$_SESSION['loggedin'] = false;
$_SESSION['user'] = "";

header('Location: index.php');
exit();

?>