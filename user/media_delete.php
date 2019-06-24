<?php
require("../components/database.php");
session_start();

if($_GET['ajax'])
{
	if(isset($_GET['id_user']) && isset($_GET['id_media']) && isset($_GET['hash']))
	{
		$id_user = $mysqli->escape_string($_GET['id_user']);
		$id_media = $mysqli->escape_string($_GET['id_media']);
		$hash = $mysqli->escape_string($_GET['hash']);
		
		$result = $mysqli->query("SELECT * FROM users WHERE id='$id_user' AND hash='$hash' AND admin='1'") or die($mysqli->error);
		
		if ($result->num_rows == 1)
		{
			$result = $mysqli->query("DELETE FROM related_media WHERE id_1='$id_media'") or die($mysqli->error);
			$result = $mysqli->query("DELETE FROM related_media WHERE id_2='$id_media'") or die($mysqli->error);
			$result = $mysqli->query("DELETE FROM related_staff WHERE id_1='$id_media'") or die($mysqli->error);
			$result = $mysqli->query("DELETE FROM media WHERE id='$id_media'") or die($mysqli->error);
			
			$_SESSION['message'] = "Media o identyfikatorze ".$id_media." i wszytkie powiązania zostały usunięte.";
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