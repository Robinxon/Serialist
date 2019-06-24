<?php
require("../components/database.php");
session_start();

if(isset($_SESSION['logged']))
	if($_SESSION['logged'])
		header("Location: http://localhost/browse/");
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

		<title>Serialist</title>
	</head>
	<body class="text-center">
		<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
			<?php require("../components/header.php") ?>

			<main role="main" class="inner cover">
				<?php require("../components/alerts.php") ?>
				<h1 class="cover-heading">Twój asystent w oglądaniu</h1>
				<p class="lead">Aby korzystać ze wszystkich funkcji serwisu potrzebujesz darmowego konta.</p>
				<p class="lead">
					<a href="../register/" class="btn btn-lg btn-secondary">Stwórz konto</a>
				</p>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script>
			$('[href*="../home/"]').addClass('active');
		</script>
	</body>
</html>