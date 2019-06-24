<?php
require("../components/database.php");
session_start();

if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash']))
{
	$email = $mysqli->escape_string($_GET['email']);
	$hash = $mysqli->escape_string($_GET['hash']);
	
	$result = $mysqli->query("SELECT * FROM users WHERE email='$email' AND hash='$hash' AND active='1'");
	
	if ($result->num_rows == 0)
	{
		$_SESSION['message'] = "Nie ma takiego użytkownika lub konto jest nieaktywne!";
		$_SESSION['message_type'] = "warning";
		session_write_close();
		header("Location: http://localhost/home/");
	}
}
else
{
	$_SESSION['message'] = "Adres URL jest nieprawidłowy!";
	$_SESSION['message_type'] = "warning";
	session_write_close();
	header("Location: http://localhost/home/");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if($_POST['password'] == $_POST['passwordRepeat'])
	{
		$new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
		$hash = $mysqli->escape_string(md5(rand(0,999999)));
		
		$sql = "UPDATE users SET password='$new_password', hash='$hash' WHERE email='$email'";
		
		if ($mysqli->query($sql))
		{
			$_SESSION['message'] = "Hasło zostało zmienione!";
			$_SESSION['message_type'] = "success";
			session_write_close();
			header("Location: http://localhost/home/");
		}
		else
		{
			$_SESSION['message'] = "Nie udało się zmienić hasła!";
			$_SESSION['message_type'] = "danger";
			session_write_close();
			header("Location: http://localhost/home/");
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
		<base href='http://localhost/'>

		<!-- CSS -->
		<link href="../css/bootstrap.min.css" type="text/css" rel="stylesheet">
		<link href="../css/cover.css" type="text/css" rel="stylesheet">
		<link href="../css/login.css" type="text/css" rel="stylesheet">

		<title>Serialist</title>
	</head>
	<body class="text-center">
		<div class="xd cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
			<?php require("../components/header.php") ?>

			<main role="main" class="inner cover">
				<?php require("../components/alerts.php") ?>
				<form class="form-signin" action="" method="post">
					<h1 class="h3 mb-3 font-weight-normal">Resetowanie hasła</h1>
					<label for="password" class="sr-only">Nowe hasło</label>
					<input type="password" name="password" class="form-control" placeholder="Hasło" required autofocus>
					<label for="passwordRepeat" class="sr-only">Powtórz hasło</label>
					<input type="password" name="passwordRepeat" class="form-control" placeholder="Powtórz hasło" required>
					<button class="btn btn-lg btn-primary btn-block" type="submit">Reset</button>
				</form>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
	</body>
</html>