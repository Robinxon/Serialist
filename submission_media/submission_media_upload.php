<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require("../components/database.php");

//test
echo json_encode(print_r($_POST));

//initializating variables
$id_submission = "";
$title = "";
$description = "";
$poster = '';
$type = "";
$premiere = "";
$episodes = "";
$duration = "";
$id_user = "";
$id_media = "";
$relations_count = "";
$staff_count = "";
$date_added = date("Y-m-d H:i:s");
$ajax = false;
$accepted = 0;

//escaping strings
if(isset($_POST['id_submission']))
	$id_submission = $mysqli->escape_string($_POST['id_submission']);
if(isset($_POST['title']))
	$title = $mysqli->escape_string($_POST['title']);
if(isset($_POST['description']))
	$description = $mysqli->escape_string($_POST['description']);
if(isset($_POST['poster']))
	$poster = $mysqli->escape_string($_POST['poster']);
if(isset($_POST['type']))
	$type = $mysqli->escape_string($_POST['type']);
if(isset($_POST['premiere']))
	$premiere = $mysqli->escape_string($_POST['premiere']);
if(isset($_POST['episodes']))
	$episodes = $mysqli->escape_string($_POST['episodes']);
if(isset($_POST['duration']))
	$duration = $mysqli->escape_string($_POST['duration']);
if(isset($_POST['id_user']))
	$id_user = $mysqli->escape_string($_POST['id_user']);
if(isset($_POST['id_media']))
	$id_media = $mysqli->escape_string($_POST['id_media']);
if(isset($_POST['relations_count']))
	$relations_count = $mysqli->escape_string($_POST['relations_count']);
if(isset($_POST['staff_count']))
	$staff_count = $mysqli->escape_string($_POST['staff_count']);
if(isset($_POST['ajax']))
	$ajax = $mysqli->escape_string($_POST['ajax']);
if(isset($_POST['accepted']))
	$accepted = $mysqli->escape_string($_POST['accepted']);

if($id_media == 0)
	$last_id = 0;
else
	$last_id = $id_media;

if($accepted == 1)
{
	$genres = array();
	for ($x = 1; $x <= 14; $x++)
	{
		$genre = 'genre'.$x;
		if(isset($_POST[$genre]))
			$genres[] = $x;
	}

	$relations = array();
	$relations_types = array();
	for ($x = 1; $x <= $relations_count; $x++)
	{
		$relation = 'relation'.$x;
		$relation_type = 'relation'.$x.'_type';
		if(!empty($_POST[$relation]))
		{
			$relations[] = $mysqli->escape_string($_POST[$relation]);
			$relations_types[] = $mysqli->escape_string($_POST[$relation_type]);
		}
	}

	$staffs = array();
	$staffs_types = array();
	$staffs_as = array();
	for ($x = 1; $x <= $staff_count; $x++)
	{
		$staff = 'staff'.$x;
		$staff_type = 'staff'.$x.'_type';
		$staff_as = 'staff'.$x.'_as';
		if(!empty($_POST[$staff]))
		{
			$staffs[] = $mysqli->escape_string($_POST[$staff]);
			$staffs_types[] = $mysqli->escape_string($_POST[$staff_type]);
			$staffs_as[] = $mysqli->escape_string($_POST[$staff_as]);
		}
	}

	$genre_upload = '|';
	$x = 1;
	foreach($genres as $genre)
	{
		$genre_upload .= $genre;
		if($x < count($genres)) {$genre_upload .= '|';}
		$x++;
	}
	$genre_upload .= '|';
	
	$query = "SELECT * FROM media WHERE id='$id_media'";
	$result = $mysqli->query($query) or die($mysqli->error);
	if($result->num_rows == 0)
	{
		if(empty($poster))
		{
			$query = "INSERT INTO media VALUES ('', '$title', '$description','', '$type', '$premiere', '$episodes', '$duration', '$genre_upload')";
			$mysqli->query($query) or die($mysqli->error);
		}
		else
		{
			$query = "INSERT INTO media VALUES ('', '$title', '$description', '', '$type', '$premiere', '$episodes', '$duration', '$genre_upload')";
			$mysqli->query($query) or die($mysqli->error);
			
			$last_id = mysqli_insert_id($mysqli);
			$tmp_file = $poster;
			$extension = end(explode('.', $poster));
			$poster = str_replace(' ', '-', $title); // Replaces all spaces with hyphens.
			$photo = preg_replace('/[^A-Za-z0-9\-]/', '', $poster); // Removes special chars.
			$poster = 'm'.$last_id.'-'.$poster.'.'.$extension; //Make file path with designed template.
			$poster = strtolower($poster); //Lower all characters in string.
			
			$query = "UPDATE media SET poster='$poster' WHERE id='$last_id'";
			$mysqli->query($query) or die($mysqli->error);
			
			rename('../media_edit/'.$tmp_file, '../files/images/cover-large/'.$poster);
		}
	}
	else
	{
		if(empty($poster))
		{
			$query = "UPDATE media SET title='$title', description='$description', type='$type', premiere='$premiere', episodes='$episodes', duration='$duration', genre='$genre_upload' WHERE id='$id_media'";
			$mysqli->query($query) or die($mysqli->error);
		}
		else
		{
			$tmp_file = $poster;
			$extension = end(explode('.', $poster));
			$poster = str_replace(' ', '-', $title); // Replaces all spaces with hyphens.
			$photo = preg_replace('/[^A-Za-z0-9\-]/', '', $poster); // Removes special chars.
			$poster = 'm'.$id_media.'-'.$poster.'.'.$extension; //Make file path with designed template.
			$poster = strtolower($poster); //Lower all characters in string.
			
			
			rename('../media_edit/'.$tmp_file, '../files/images/cover-large/'.$poster);
		}
	}
	
	//akcje na related media
	$query = "DELETE FROM related_media WHERE id_1='$last_id'";
	$mysqli->query($query) or die($mysqli->error);
	$query = "DELETE FROM related_media WHERE id_2='$last_id'";
	$mysqli->query($query) or die($mysqli->error);
	
	$x = 0;
	foreach($relations as $relation)
	{
		$query = "SELECT id FROM media WHERE title='$relation'";
		$result = $mysqli->query($query) or die($mysqli->error);
		$related = $result->fetch_assoc();
		$related_id = $related['id'];
		if ($relations_types[$x] == '3' || $relations_types[$x] == '4')
		{
			$relation_type = $relations_types[$x];
			$query = "INSERT INTO related_media VALUES('', '$last_id', '$related_id', '$relation_type')";
			$mysqli->query($query) or die($mysqli->error);
			$query = "INSERT INTO related_media VALUES('', '$related_id', '$last_id', '$relation_type')";
			$mysqli->query($query) or die($mysqli->error);
		}
		else
		{
			if($relations_types[$x] == '1')
			{
				$query = "INSERT INTO related_media VALUES('', '$last_id', '$related_id', '1')";
				$mysqli->query($query) or die($mysqli->error);
				$query = "INSERT INTO related_media VALUES('', '$related_id', '$last_id', '2')";
				$mysqli->query($query) or die($mysqli->error);
			}
			
			if($relations_types[$x] == '2')
			{
				$query = "INSERT INTO related_media VALUES('', '$last_id', '$related_id', '2')";
				$mysqli->query($query) or die($mysqli->error);
				$query = "INSERT INTO related_media VALUES('', '$related_id', '$last_id', '1')";
				$mysqli->query($query) or die($mysqli->error);
			}
			
		}
		$x++;
	}

	//akcje na related staff
	$query = "DELETE FROM related_staff WHERE id_1='$last_id'";
	$mysqli->query($query) or die($mysqli->error);
	
	$x = 0;
	foreach($staffs as $staff)
	{
		$query = "SELECT id FROM staff WHERE name='$staff'";
		$result = $mysqli->query($query) or die($mysqli->error);
		$related = $result->fetch_assoc();
		$staff_id = $related['id'];
		$staffs_type = $staffs_types[$x];
		if(empty($staffs_as[$x]))
			$staff_as = '';
		else
			$staff_as = $staffs_as[$x];
		$query = "INSERT INTO related_staff VALUES('', '$last_id', '$staff_id', '$staffs_type', '$staff_as')";
		$mysqli->query($query) or die($mysqli->error);
		$x++;
	}
	
	$query = "UPDATE submissions_media SET status='2', poster='$poster' WHERE id='$id_submission'";
	$result = $mysqli->query($query) or die($mysqli->error);
	
	$_SESSION['message'] = "Zgłoszenie numer ".$id_submission." zostało zaakceptowane.";
	$_SESSION['message_type'] = "info";
	session_write_close();
}
else
{
	$query = "UPDATE submissions_media SET status='3' WHERE id='$id_submission'";
	$result = $mysqli->query($query) or die($mysqli->error);
	
	$_SESSION['message'] = "Zgłoszenie numer ".$id_submission." zostało odrzucone.";
	$_SESSION['message_type'] = "info";
	session_write_close();
}
?>