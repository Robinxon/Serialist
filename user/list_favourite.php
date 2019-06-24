<?php
require("../components/database.php");
session_start();

if($_GET['ajax'])
{
	if(isset($_GET['id_user']) && isset($_GET['id_media']) && isset($_GET['hash']) && isset($_GET['favourite']))
	{
		$id_user = $mysqli->escape_string($_GET['id_user']);
		$id_media = $mysqli->escape_string($_GET['id_media']);
		$hash = $mysqli->escape_string($_GET['hash']);
		$favourite = $mysqli->escape_string($_GET['favourite']);
		
		$result = $mysqli->query("SELECT * FROM users WHERE id='$id_user' AND hash='$hash'") or die($mysqli->error);
		
		if ($result->num_rows == 1)
		{
			$result = $mysqli->query("UPDATE users_list SET favourite='$favourite' WHERE id_user='$id_user' AND id_media='$id_media'") or die($mysqli->error);
		}
		else
		{
			echo json_encode('error no record');
		}
	}
	else
	{
		echo json_encode('error no data');
	}
}
else
{
	echo json_encode('error no ajax');
}
?>