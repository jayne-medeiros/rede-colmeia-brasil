<?php
session_start();
session_destroy();
header("Location: produtores.php");
exit;
?>