<?php
session_start();
session_unset();
session_destroy();
header("Location: studentLogin.php");
exit();
?>
