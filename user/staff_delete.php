<?php
require("../components/database.php");
session_start();

if($_GET['ajax'])
{
	if(isset($_GET['id_user']) && isset($_GET['id_staff']) && isset($_GET['hash']))
	{
		$id_user = $mysqli->escape_string($_GET['id_user']);
		$id_staff = $mysqli->escape_string($_GET['id_staff']);
		$hash = $mysqli->escape_string($_GET['hash']);
		
		$result = $mysqli->query("SELECT * FROM users WHERE id='$id_user' AND hash='$hash' AND admin='1'") or die($mysqli->error);
		
		if ($result->num_rows == 1)
		{
			$result = $mysqli->query("DELETE FROM related_staff WHERE id_2='$id_staff'") or die($mysqli->error);
			$result = $mysqli->query("DELETE FROM staff WHERE id='$id_staff'") or die($mysqli->error);
			
			$_SESSION['message'] = "Obsada o identyfikatorze ".$id_staff." i wszytkie powiązania zostały usunięte.";
			$_SESSION['message_type'] = "info";
			session_write_close();
		}
		else
		{
			echo json_encode('error');
		}
	}
	else
	{
		echo json_encode('error');
	}
}
else
{
	echo json_encode('error');
}
?>