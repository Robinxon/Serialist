<?php
require("../components/database.php");
session_start();

if(isset($_SESSION['logged']))
	if($_SESSION['logged'])
		header("Location: http://localhost/browse/");

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$email = $mysqli->escape_string($_POST['email']);
	$result = $mysqli->query("SELECT * FROM users WHERE email='$email'");

	if ($result->num_rows == 0)
	{
		$_SESSION['message'] = "Jeśli użytkownik z takim adresem email istnieje to został mu wysłany link do resetowania hasła.";
		$_SESSION['message_type'] = "info";
	}
	else
	{
		$user = $result->fetch_assoc();
		
		$email = $user['email'];
		$hash = $user['hash'];
		$username = $user['username'];
		$subject = "Reset hasła - Serialist";
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		$message = '<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body>
					Witaj, '.$username.'<br><br>Resetowanie hasła:<br>http://localhost/reset/'.urlencode($email).'/'.urlencode($hash).'/</body></html>';
		mail($email, $subject, $message, $headers);
		
		$_SESSION['message'] = "Jeśli użytkownik z takim adresem email istnieje to został mu wysłany link do resetowania hasła.";
		$_SESSION['message_type'] = "info";
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
				<form class="form-signin" action="" method="post">
					<h1 class="h3 mb-3 font-weight-normal">Resetowanie hasła</h1>
					<label for="email" class="sr-only">Adres email</label>
					<input type="email" name="email" class="form-control" placeholder="Adres email" required autofocus>
					<button class="btn btn-lg btn-primary btn-block" type="submit">Resetuj hasło</button>
				</form>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
	</body>
</html>