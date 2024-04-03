<?php
session_start();
$_SESSION['loggedIn'] = false;
header('Location: userProfile.php');
setcookie("loggedOut", 0, time() + 1, "/");
?>