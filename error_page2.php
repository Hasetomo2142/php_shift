<?php
session_start();
$str = "Location: ./".$_SESSION['destination'];
header($str);
exit();

?>