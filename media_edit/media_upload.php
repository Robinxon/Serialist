<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require("../components/database.php");

//test
//echo json_encode(print_r($_POST));

//initializating variables
$title = "";
$description = "";
$type = "";
$premiere = "";
$episodes = "";
$duration = "";
$id_user = "";
$hash = "";
$id_media = "";
$relations_count = "";
$staff_count = "";
$date_added = date("Y-m-d H:i:s");
$ajax = false;

//escaping strings
if(isset($_POST['title']))
	$title = $mysqli->escape_string($_POST['title']);
if(isset($_POST['description']))
	$description = $mysqli->escape_string($_POST['description']);
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
if(isset($_POST['hash']))
	$hash = $mysqli->escape_string($_POST['hash']);
if(isset($_POST['id_media']))
	$id_media = $mysqli->escape_string($_POST['id_media']);
if(isset($_POST['relations_count']))
	$relations_count = $mysqli->escape_string($_POST['relations_count']);
if(isset($_POST['staff_count']))
	$staff_count = $mysqli->escape_string($_POST['staff_count']);
if(isset($_POST['ajax']))
	$ajax = $mysqli->escape_string($_POST['ajax']);

if(!$ajax) {die();}

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

//start file
$target_dir = "tmp/";
$target_file = $target_dir . basename($_FILES["poster"]["name"]);
$filename = uniqid('', true);
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$target_filename = $target_dir.$filename.'.'.$imageFileType;
$uploadOk = 1;
$poster_path = '';

if($_FILES['poster']['error'] == 0) {
    $check = getimagesize($_FILES["poster"]["tmp_name"]);
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
	
	if ($_FILES["poster"]["size"] > 2000000)
	{
		echo json_encode("Sorry, your file is too large.</br>");
		$uploadOk = 0;
	}
	
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "bmp" )
	{
		echo json_encode("Sorry, only JPG, JPEG, PNG, GIF @ BMP files are allowed.</br>");
		$uploadOk = 0;
	}
	
	if ($uploadOk == 0)
	{
		echo json_encode("Sorry, your file was not uploaded.</br>");
	}
	else
	{
		if (move_uploaded_file($_FILES["poster"]["tmp_name"], $target_filename))
		{
			echo json_encode("The file ". basename( $_FILES["poster"]["name"]). " has been uploaded.</br>");
			$poster_path = $target_filename;
		}
		else
		{
			echo json_encode("Sorry, there was an error uploading your file.</br>");
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
	$genre_upload = '';
	$x = 1;
	foreach($genres as $genre)
	{
		$genre_upload .= $genre;
		if($x < count($genres)) {$genre_upload .= '|';}
		$x++;
	}
	
	$relations_upload = '';
	$x = 1;
	foreach($relations as $relation)
	{
		$relations_upload .= str_replace("|"," ",$relation);
		if($x < count($relations)) {$relations_upload .= '|';}
		$x++;
	}
	
	$relations_types_upload = '';
	$x = 1;
	foreach($relations_types as $relation_type)
	{
		$relations_types_upload .= str_replace("|"," ",$relation_type);
		if($x < count($relations_types)) {$relations_types_upload .= '|';}
		$x++;
	}
	
	$staffs_upload = '';
	$x = 1;
	foreach($staffs as $staff)
	{
		$staffs_upload .= str_replace("|"," ",$staff);
		if($x < count($staffs)) {$staffs_upload .= '|';}
		$x++;
	}
	
	$staffs_types_upload = '';
	$x = 1;
	foreach($staffs_types as $staff_type)
	{
		$staffs_types_upload .= str_replace("|"," ",$staff_type);
		if($x < count($staffs_types)) {$staffs_types_upload .= '|';}
		$x++;
	}
	
	$staffs_as_upload = '';
	$x = 1;
	foreach($staffs_as as $staff_as)
	{
		$staffs_as_upload .= str_replace("|"," ",$staff_as);
		if($x < count($staffs_as)) {$staffs_as_upload .= '|';}
		$x++;
	}
	
	$query = "INSERT INTO submissions_media VALUES ('', '$id_user', '$id_media', '$date_added', '$title', '$description', '$poster_path', '$type', '$premiere', '$episodes', '$duration', '$genre_upload', '$relations_upload', '$relations_types_upload', '$staffs_upload', '$staffs_types_upload', '$staffs_as_upload', '1')";
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