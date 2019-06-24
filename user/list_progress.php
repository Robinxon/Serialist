<?php
require("../components/database.php");
session_start();

if($_GET['ajax'])
{
	if(isset($_GET['id_user']) && isset($_GET['id_media']) && isset($_GET['hash']) && isset($_GET['progress']))
	{
		$id_user = $mysqli->escape_string($_GET['id_user']);
		$id_media = $mysqli->escape_string($_GET['id_media']);
		$hash = $mysqli->escape_string($_GET['hash']);
		$progress = $mysqli->escape_string($_GET['progress']);
		
		$result = $mysqli->query("SELECT * FROM users WHERE id='$id_user' AND hash='$hash'") or die($mysqli->error);
		
		if ($result->num_rows == 1)
		{
			$result = $mysqli->query("SELECT episodes FROM media WHERE id='$id_media'") or die($mysqli->error);
			$media = $result->fetch_assoc();
			
			$mysqli->query("UPDATE users_list SET progress='$progress' WHERE id_user='$id_user' AND id_media='$id_media'") or die($mysqli->error);
			
			$result = $mysqli->query("SELECT progress FROM users_list WHERE id_user='$id_user' AND id_media='$id_media'") or die($mysqli->error);
			$users_list = $result->fetch_assoc();
			
			if($users_list['progress'] >= $media['episodes'])
			{
				$mysqli->query("UPDATE users_list SET status=2 WHERE id_user='$id_user' AND id_media='$id_media'") or die($mysqli->error);
			}
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