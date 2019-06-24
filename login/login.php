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
		$_SESSION['message'] = "Nieprawidłowe dane logowania!";
		$_SESSION['message_type'] = "warning";
	}
	else
	{
		$user = $result->fetch_assoc();
		
		if(password_verify($_POST['password'], $user['password']))
		{
			if($user['active'] == 1)
			{
				$_SESSION['id_user'] = $user['id'];
				$_SESSION['username'] = $user['username'];
				$_SESSION['email'] = $user['email'];
				$_SESSION['avatar'] = $user['avatar'];
				$_SESSION['hash'] = $user['hash'];
				$_SESSION['admin'] = $user['admin'];
				$_SESSION['logged'] = true;
				
				header("Location: http://localhost/browse/");
			}
			else
			{
				$_SESSION['message'] = "Konto nie zostało jeszcze aktywowane!";
				$_SESSION['message_type'] = "warning";
			}
		}
		else
		{
			$_SESSION['message'] = "Nieprawidłowe dane logowania!";
			$_SESSION['message_type'] = "warning";
		}
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
					<h1 class="h3 mb-3 font-weight-normal">Logowanie</h1>
					<label for="email" class="sr-only">Adres email</label>
					<input type="email" name="email" class="form-control" placeholder="Adres email" required autofocus>
					<label for="password" class="sr-only">Hasło</label>
					<input type="password" name="password" class="form-control" placeholder="Hasło" required>
					<button class="btn btn-lg btn-primary btn-block" type="submit">Zaloguj</button>
					<a href="../forgot/">Nie pamiętasz hasła?</a></br>
					<a href="../register/">Nie masz jeszcze konta?</a>
				</form>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script>
			$('[href*="../login/"]').addClass('active');
		</script>
	</body>
</html>