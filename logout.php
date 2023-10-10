<?php
include('./config/function.php');

session_start();
unset($_SESSION['userID']);
unset($_SESSION['userName']);
unset($_SESSION['sessionID']);
unset($_SESSION['token']);
session_destroy();
goto2("login.php","You have logged out, See you again!.");
?>