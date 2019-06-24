<?php
require("../components/database.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$username = $mysqli->escape_string($_POST['username']);
	$email = $mysqli->escape_string($_POST['email']);
	$password = $mysqli->escape_string($_POST['password']);
	$password_repeat = $mysqli->escape_string($_POST['passwordRepeat']);
	$password_encrypted = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
	$hash = $mysqli->escape_string(md5(rand(0,999999)));
	
	if($password == $password_repeat)
	{
		if(strlen($password) < 8)
		{
			$_SESSION['message'] = "Hasło jest za krótkie! (minimum 8 znaków)";
			$_SESSION['message_type'] = "warning";
		}
		else
		{
			$result = $mysqli->query("SELECT * FROM users WHERE email='$email'") or die($mysqli->error());
		
			if ($result->num_rows > 0)
			{
				$_SESSION['message'] = "Konto z podanym mailem już istnieje!";
				$_SESSION['message_type'] = "warning";
			}
			else
			{
				$sql = "INSERT INTO users (username, email, password, hash) "
						. "VALUES ('$username', '$email', '$password_encrypted', '$hash')";
						
				if ($mysqli->query($sql))
				{
					$headers = "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
					$message = '<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body>
					Witaj, '.$username.'<br><br>Resetowanie hasła:<br>http://localhost/activate/'.urlencode($email).'/'.urlencode($hash).'/</body></html>';
					$subject = "Aktywacja konta - Serialist";
					mail($email, $subject, $message, $headers);
					
					$_SESSION['message'] = "Na podany adres email został wysłany link aktywacyjny.";
					$_SESSION['message_type'] = "success";
				}
				else
				{
					$_SESSION['message'] = "Nie udało się utworzyć konta!";
					$_SESSION['message_type'] = "danger";
				}
			}
		}
	}
	else
	{
		$_SESSION['message'] = "Podane hasła nie są takie same!";
		$_SESSION['message_type'] = "warning";
	}
}
?>
<!doctype html>
<html lang="pl">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- CSS -->
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/cover.css" rel="stylesheet">
		<link href="../css/login.css" rel="stylesheet">

		<title>Serialist</title>
	</head>
	<body class="text-center">
		<div class="xd cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
			<?php require("../components/header.php") ?>

			<main role="main" class="inner cover">
				<?php require("../components/alerts.php") ?>
				<form class="form-signin" method="post" action="">
					<h1 class="h3 mb-3 font-weight-normal">Tworzenie konta</h1>
					<div class="form-group">
						<label for="inputUsername">Nazwa użytkownika</label>
						<input type="text" id="inputUsername" class="form-control" name="username" required autofocus>
					</div>
					<div class="form-group">
						<label for="inputEmail">Adres email</label>
						<input type="email" id="inputEmail" class="form-control" name="email" required>
					</div>
					<div class="form-group">
						<label for="inputPassword">Hasło</label>
						<input type="password" id="inputPassword" class="form-control" name="password" required>
					</div>
					<div class="form-group">
						<label for="inputPasswordRepeat">Powtórz hasło</label>
						<input type="password" id="inputPasswordRepeat" class="form-control" name="passwordRepeat" required>
					</div>
					<button class="btn btn-lg btn-primary btn-block" type="submit">Stwórz konto</button>
				</form>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
	</body>
</html>