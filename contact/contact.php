<?php
require("../components/database.php");
session_start();

if(isset($_SESSION['logged']))
	if($_SESSION['logged'])
		header("Location: http://localhost/browse/");
	
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$email = $mysqli->escape_string($_POST['email']);
	$topic = $mysqli->escape_string($_POST['topic']);
	$content = $mysqli->escape_string($_POST['content']);
	
	if(!empty($email) && !empty($topic) && !empty($content))
	{
		$subject = "Kontakt: ".$topic." - Serialist";
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		$message = '<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body>
					Treść wiadomości: '.$content.'</br>Email zwrotny: '.$email.'</body></html>';
		mail($email, $subject, $message, $headers);
		
		$_SESSION['message'] = "Wiadomość została wysłana. Odpowiedź wyślemy na adres mailowy podany w formularzu.";
		$_SESSION['message_type'] = "success";
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
		<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
			<?php require("../components/header.php") ?>
			<main role="main" class="inner cover">
				<?php require("../components/alerts.php") ?>
				<form class="form-signin" action="../contact/" method="post">
					<h1 class="h3 mb-3 font-weight-normal">Kontakt</h1>
					<div class="form-group">
						<label for="inputEmail">Adres e-mail</label>
						<input type="email" class="form-control" id="inputEmail" name="email" required autofocus>
					</div>
					<div class="form-group">
						<label for="inputTopic">Temat</label>
						<input type="text" class="form-control" id="inputTopic" name="topic" required>
					</div>
					<div class="form-group">
						<label for="TextContent">Treść</label>
						<textarea class="form-control" id="TextContent" rows="5" name="content" required></textarea>
					</div>
					<button class="btn btn-lg btn-primary btn-block" type="submit">Wyslij</button>
				</form>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script>
			$('[href*="../contact/"]').addClass('active');
		</script>
	</body>
</html>