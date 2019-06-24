<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require("../components/database.php");

//test
echo json_encode(print_r($_POST));

//initializating variables
$id_submission = "";
$name = "";
$description = "";
$poster = '';
$id_user = "";
$id_staff = "";
$date_added = date("Y-m-d H:i:s");
$ajax = false;
$accepted = 0;

//escaping strings
if(isset($_POST['id_submission']))
	$id_submission = $mysqli->escape_string($_POST['id_submission']);
if(isset($_POST['name']))
	$name = $mysqli->escape_string($_POST['name']);
if(isset($_POST['description']))
	$description = $mysqli->escape_string($_POST['description']);
if(isset($_POST['photo']))
	$photo = $mysqli->escape_string($_POST['photo']);
if(isset($_POST['id_user']))
	$id_user = $mysqli->escape_string($_POST['id_user']);
if(isset($_POST['id_staff']))
	$id_staff = $mysqli->escape_string($_POST['id_staff']);
if(isset($_POST['ajax']))
	$ajax = $mysqli->escape_string($_POST['ajax']);
if(isset($_POST['accepted']))
	$accepted = $mysqli->escape_string($_POST['accepted']);

if($id_staff == 0)
	$last_id = 0;
else
	$last_id = $id_staff;

if($accepted == 1)
{
	$query = "SELECT * FROM staff WHERE id='$id_staff'";
	$result = $mysqli->query($query) or die($mysqli->error);
	if($result->num_rows == 0)
	{
		if(empty($photo))
		{
			$query = "INSERT INTO staff VALUES ('', '$name', '$description', ''";
			
		}
		else
		{
			$query = "INSERT INTO staff VALUES ('', '$name', '$description', '')";
			$mysqli->query($query) or die($mysqli->error);
			
			$last_id = mysqli_insert_id($mysqli);
			$tmp_file = $photo;
			$extension = end(explode('.', $photo));
			$photo = str_replace(' ', '-', $name); // Replaces all spaces with hyphens.
			$photo = preg_replace('/[^A-Za-z0-9\-]/', '', $photo); // Removes special chars.
			$photo = 's'.$last_id.'-'.$photo.'.'.$extension; //Make file path with designed template.
			$photo = strtolower($photo); //Lower all characters in string.
			
			$query = "UPDATE staff SET photo='$photo' WHERE id='$last_id'";
			$mysqli->query($query) or die($mysqli->error);
			
			rename('../staff_edit/'.$tmp_file, '../files/images/photo/'.$photo);
		}
	}
	else
	{
		if(empty($photo))
		{
			$query = "UPDATE staff SET name='$name', description='$description' WHERE id='$last_id'";
			$mysqli->query($query) or die($mysqli->error);
		}
		else
		{
			$tmp_file = $photo;
			$extension = end(explode('.', $photo));
			$photo = str_replace(' ', '-', $name); // Replaces all spaces with hyphens.
			$photo = preg_replace('/[^A-Za-z0-9\-]/', '', $photo); // Removes special chars.
			$photo = 's'.$last_id.'-'.$photo.'.'.$extension; //Make file path with designed template.
			$photo = strtolower($photo); //Lower all characters in string.
			
			$query = "UPDATE staff SET name='$name', description='$description', photo='$photo' WHERE id='$last_id'";
			$mysqli->query($query) or die($mysqli->error);
			
			rename('../staff_edit/'.$tmp_file, '../files/images/photo/'.$photo);
		}
	}
	
	$query = "UPDATE submissions_staff SET status='2' WHERE id='$id_submission'";
	$result = $mysqli->query($query) or die($mysqli->error);
	
	$_SESSION['message'] = "Zgłoszenie numer ".$id_submission." zostało zaakceptowane.";
	$_SESSION['message_type'] = "info";
	session_write_close();
}
else
{
	$query = "UPDATE submissions_staff SET status='3' WHERE id='$id_submission'";
	$result = $mysqli->query($query) or die($mysqli->error);
	
	$_SESSION['message'] = "Zgłoszenie numer ".$id_submission." zostało odrzucone.";
	$_SESSION['message_type'] = "info";
	session_write_close();
}
?>