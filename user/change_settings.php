<?php
require("../components/database.php");
header('Content-Type: text/html; charset=utf-8');
session_start();

$username = "";
$email = "";
$new_password = "";
$repeat_password = "";
$ajax = "";

if(isset($_POST['username']))
	$username = $mysqli->escape_string($_POST['username']);
if(isset($_POST['email']))
	$email = $mysqli->escape_string($_POST['email']);
if(isset($_POST['new_password']))
	$new_password = $mysqli->escape_string($_POST['new_password']);
if(isset($_POST['repeat_password']))
	$repeat_password = $mysqli->escape_string($_POST['repeat_password']);
if(isset($_POST['ajax']))
	$ajax = $mysqli->escape_string($_POST['ajax']);

$id_user = $_SESSION['id_user'];
$query = "SELECT * FROM users WHERE id = '$id_user'";
$result = $mysqli->query($query) or die($mysqli->error());
$user = $result->fetch_assoc();
$id_user = $user['id'];

if($_POST['ajax'])
{
	//username
	if($user['username'] != $username)
	{
		if(!empty($username))
		{
			$query = "SELECT * FROM users WHERE username = '$username'";
			$result = $mysqli->query($query) or die($mysqli->error());
			if($result->num_rows == 0)
			{
				$query = "UPDATE users SET username='$username' WHERE id = '$id_user'";
				$mysqli->query($query) or die($mysqli->error());
			}
			else
			{
				$_SESSION['message'] = "Wybrana nazwa użytkownika jest zajęta!";
				$_SESSION['message_type'] = "warning";
				session_write_close();
				die();
			}
		}
		else
		{
			$_SESSION['message'] = "Nazwa użytkownika nie może być pusta!";
			$_SESSION['message_type'] = "warning";
			session_write_close();
			die();
		}
	}
	
	//email
	if($user['email'] != $email)
	{
		if(!empty($email))
		{
			$query = "SELECT * FROM users WHERE email = '$email'";
			$result = $mysqli->query($query) or die($mysqli->error());
			if($result->num_rows == 0)
			{
				$query = "UPDATE users SET email='$email', active='0' WHERE id = '$id_user'";
				$mysqli->query($query) or die($mysqli->error());
			}
			else
			{
				$_SESSION['message'] = "Wybrany adres email jest już używany!";
				$_SESSION['message_type'] = "warning";
				session_write_close();
				die();
			}
		}
		else
		{
			$_SESSION['message'] = "Adres email nie może być pusty!";
			$_SESSION['message_type'] = "warning";
			session_write_close();
			die();
		}
	}
	
	//password
	if(!empty($new_password))
	{
		if(strlen($new_password) >= 8)
		{
			if($new_password == $repeat_password)
			{
				$password = password_hash($new_password, PASSWORD_BCRYPT);
				$query = "UPDATE users SET password='$password' WHERE id = '$id_user'";
				$mysqli->query($query) or die($mysqli->error());
			}
			else
			{
				$_SESSION['message'] = "Podane hasła nie są identyczne!";
				$_SESSION['message_type'] = "warning";
				session_write_close();
				die();
			}
		}
		else
		{
			$_SESSION['message'] = "Hasło musi mieć przynajmniej 8 znaków!";
			$_SESSION['message_type'] = "warning";
			session_write_close();
			die();
		}
	}
	
	//start file
	$target_file = basename($_FILES["avatar"]["name"]);
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	$uploadOk = 0;

	if($_FILES['avatar']['error'] == 0) {
		$check = getimagesize($_FILES["avatar"]["tmp_name"]);
		if($check !== false)
		{
			$uploadOk = 1;
		}
		else
		{
			$_SESSION['message'] = "Niedozwolony typ pliku!";
			$_SESSION['message_type'] = "warning";
			session_write_close();
			die();
		}
		
		if ($_FILES["avatar"]["size"] > 2000000)
		{
			$_SESSION['message'] = "Przekroczono maksymalną wielkość pliku!";
			$_SESSION['message_type'] = "warning";
			session_write_close();
			die();
		}
		
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "bmp" )
		{
			$_SESSION['message'] = "Dozwolone są pliki z rozrzerzeniach JPG, JPEG, PNG, GIF i BMP!";
			$_SESSION['message_type'] = "warning";
			session_write_close();
			die();
		}
		
		if ($uploadOk == 1)
		{
			$username = $user['username'];
			$target_dir = "../files/images/avatar/";
			$extension = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			$username = str_replace(' ', '-', $username); // Replaces all spaces with hyphens.
			$username = preg_replace('/[^A-Za-z0-9\-]/', '', $username);
			$username = strtolower($username);
			$file_name = 'u'.$id_user.'-'.$username.'.'.$extension;
			$target_file = $target_dir.$file_name;
			
			if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file))
			{
				$query = "UPDATE users SET avatar='$file_name' WHERE id='$id_user'";
				$mysqli->query($query) or die($mysqli->error);
			}
			else
			{
				$_SESSION['message'] = "Wystąpił błąd podczas przesyłania pliku!";
				$_SESSION['message_type'] = "warning";
				session_write_close();
				die();
			}
		}
	}
	//end file
}

$_SESSION['message'] = "Dane zmienione pomyślnie!";
$_SESSION['message_type'] = "success";
session_write_close();
?>