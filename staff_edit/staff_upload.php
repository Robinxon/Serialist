<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require("../components/database.php");

//test
echo json_encode(print_r($_POST));

//initializating variables
$name = "";
$description = "";
$date_added = date("Y-m-d H:i:s");

//escaping strings
if(isset($_POST['name']))
	$name = $mysqli->escape_string($_POST['name']);
if(isset($_POST['description']))
	$description = $mysqli->escape_string($_POST['description']);
if(isset($_POST['id_user']))
	$id_user = $mysqli->escape_string($_POST['id_user']);
if(isset($_POST['hash']))
	$hash = $mysqli->escape_string($_POST['hash']);
if(isset($_POST['id_staff']))
	$id_staff = $mysqli->escape_string($_POST['id_staff']);
if(isset($_POST['ajax']))
	$ajax = $mysqli->escape_string($_POST['ajax']);

if(!$ajax) {die();}

//start file
$target_dir = "tmp/";
$target_file = $target_dir . basename($_FILES["photo"]["name"]);
$filename = uniqid('', true);
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$target_filename = $target_dir.$filename.'.'.$imageFileType;
$uploadOk = 1;
$photo_path = '';

if($_FILES['photo']['error'] == 0) {
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if($check !== false)
	{
        echo json_encode("File is an image - " . $check["mime"] . ".</br>");
        $uploadOk = 1;
    }
	else
	{
        echo json_encode("File is not an image.</br>");
        $uploadOk = 0;
    }
	
	$incorrect_name = true;
	while($incorrect_name)
	{
		if (file_exists($target_filename))
		{
			echo json_encode("Sorry, file already exists.</br>");
			$filename = uniqid('', true);
			$target_filename = $target_dir.$filename.'.'.$imageFileType;
		}
		else
		{
			echo json_encode("Name ok</br>");
			echo json_encode("Filetype: ".$imageFileType."</br>");
			$incorrect_name = false;
		}
	}
	
	if ($_FILES["photo"]["size"] > 2000000)
	{
		echo json_encode("Sorry, your file is too large.</br>");
		$uploadOk = 0;
	}
	
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "bmp")
	{
		echo json_encode("Sorry, only JPG, JPEG, PNG, GIF & BMP files are allowed.</br>");
		$uploadOk = 0;
	}
	
	if ($uploadOk == 0)
	{
		echo json_encode("Sorry, your file was not uploaded.</br>");
	}
	else
	{
		if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_filename))
		{
			echo "The file ". basename( $_FILES["photo"]["name"]). " has been uploaded.</br>";
			$photo_path = $target_filename;
		}
		else
		{
			echo "Sorry, there was an error uploading your file.</br>";
		}
	}
}
else
{
	echo json_encode("Wystapil blad podczas przesylania pliku!");
}
//end file

//check if user with provided id and hash exist
$result = $mysqli->query("SELECT * FROM users WHERE id='$id_user' AND hash='$hash'") or die($mysqli->error);
		
if ($result->num_rows == 1)
{
	$query = "INSERT INTO submissions_staff VALUES ('NULL', '$id_user', '$id_staff', '$date_added', '$name', '$description', '$photo_path', '1')";
	$result = $mysqli->query($query) or die($mysqli->error);
	$_SESSION['message'] = "Zgłoszenie zostało wysłane.";
	$_SESSION['message_type'] = "info";
	session_write_close();
}
else
{
	echo json_encode('ERROR: no valid user');
}
?>