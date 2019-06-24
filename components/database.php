<?php
$host = "localhost";
$username = "serialist";
$password = "H0RoEXvWxYElwH2Z";
$db = "serialist";

$mysqli = new mysqli($host, $username, $password, $db) or die("Connection failed: " . $conn->connect_error);

mysqli_set_charset($mysqli,"utf8");
?>