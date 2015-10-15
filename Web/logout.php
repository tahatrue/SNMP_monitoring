<?php
session_start();
unset($_SESSION['uname']);
unset($_SESSION['next']);
unset($_SESSION['mid']);
session_destroy();
header("Refresh: 0;welcome.php");
?>