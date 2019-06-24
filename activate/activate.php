<?php
require("../components/database.php");
session_start();

if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash']))
{
	$email = $mysqli->escape_string($_GET['email']);
	$hash = $mysqli->escape_string($_GET['hash']);
	
	$result = $mysqli->query("SELECT * FROM users WHERE email='$email' AND hash='$hash' AND active='0'") or die($mysqli->error);
	
	if ($result->num_rows == 0)
	{
		$_SESSION['message'] = "Konto zostało już aktywowane lub adres URL jest nieprawidłowy!";
		$_SESSION['message_type'] = "warning";
		session_write_close();
		header("Location: http://localhost/home/");
	}
	else
	{
		$mysqli->query("UPDATE users SET active='1' WHERE email='$email'") or die($mysqli->error);
		
		$_SESSION['message'] = "Twoje konto zostało aktywowane!";
		$_SESSION['message_type'] = "success";
		session_write_close();
		header("Location: http://localhost/home/");
	}
}
else
{
	$_SESSION['message'] = "Adres URL jest nieprawidłowy!";
	$_SESSION['message_type'] = "warning";
	session_write_close();
	header("Location: http://localhost/home/");
}
?>