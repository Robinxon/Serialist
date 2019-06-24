<?php
require("../components/database.php");
session_start();

if($_GET['ajax'])
{
	if(isset($_GET['id_user']) && isset($_GET['id_media']) && isset($_GET['hash']) && isset($_GET['status']))
	{
		$id_user = $mysqli->escape_string($_GET['id_user']);
		$id_media = $mysqli->escape_string($_GET['id_media']);
		$hash = $mysqli->escape_string($_GET['hash']);
		$status = $mysqli->escape_string($_GET['status']);
		
		$result = $mysqli->query("SELECT * FROM users WHERE id='$id_user' AND hash='$hash'") or die($mysqli->error);
		
		if ($result->num_rows == 1)
		{
			$result = $mysqli->query("SELECT * FROM users_list WHERE id_user='$id_user' AND id_media='$id_media'") or die($mysqli->error);
			if ($result->num_rows == 1)
			{
				if($status == 2)
				{
					$result = $mysqli->query("SELECT episodes FROM media WHERE id='$id_media'") or die($mysqli->error);
					$media = $result->fetch_assoc();
					$progress = $media['episodes'];
					$query = "UPDATE users_list SET status='$status', progress='$progress' WHERE id_user='$id_user' AND id_media='$id_media'";
					$mysqli->query($query) or die($mysqli->error);
				}
				else
				{
					$query = "UPDATE users_list SET status='$status' WHERE id_user='$id_user' AND id_media='$id_media'";
					$mysqli->query($query) or die($mysqli->error);
				}
			}
			else
			{
				if($status == 2)
				{
					$result = $mysqli->query("SELECT episodes FROM media WHERE id='$id_media'") or die($mysqli->error);
					$media = $result->fetch_assoc();
					$progress = $media['episodes'];
					$query = "INSERT INTO users_list (id_user, id_media, status, progress) VALUES ('$id_user', '$id_media', '$status', '$progress')";
					$mysqli->query($query) or die($mysqli->error);
					
				}
				else
				{
					$query = "INSERT INTO users_list (id_user, id_media, status) VALUES ('$id_user', '$id_media', '$status')";
					$mysqli->query($query) or die($mysqli->error);
				}
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
}
else
{
	echo json_encode('error');
}
?>