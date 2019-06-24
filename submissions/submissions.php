<?php
require("../components/database.php");
session_start();

if(!isset($_SESSION['logged']) || !$_SESSION['logged'])
{
	$_SESSION['message'] = "Musisz się zalogować, aby przejść na tę stronę!";
	$_SESSION['message_type'] = "danger";
	session_write_close();
	header("Location: http://localhost/home/");
}
else
{
	$id_user = $_SESSION['id_user'];
	$hash = $_SESSION['hash'];
	$result = $mysqli->query("SELECT * FROM users WHERE id='$id_user' AND hash='$hash' AND admin='1'") or die($mysqli->error);
		
	if ($result->num_rows != 1)
	{
		$_SESSION['message'] = "Dostęp zablokowany!";
		$_SESSION['message_type'] = "danger";
		session_write_close();
		header("Location: http://localhost/home/");
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
		<link href="../css/panel.css" rel="stylesheet">
		<link href="../css/all.css" rel="stylesheet">

		<title>Serialist</title>
	</head>
	<body>
		<div id="wrapper" class="d-flex w-100 h-100 p-3 mx-auto flex-column">
			<?php require("../components/header.php") ?>
			<main role="main" class="inner cover">
				<div class="container">
					<?php require("../components/alerts.php") ?>
					<div class="row submissions-new">
						<h4>Nowe</h4>
						<div class="table-responsive">
							<?php
							$query_media = "SELECT sm.id AS 'id', u.username AS 'username', sm.title AS 'title', sm.id_media AS 'id_media', sm.date_added AS 'date' FROM submissions_media sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN media m ON sm.id_media = m.id WHERE sm.status=1 ORDER BY sm.id DESC";
							$result_media = $mysqli->query($query_media) or die($mysqli->error());
							if ($result_media->num_rows == 0)
							{
								echo '<div class="alert alert-blue" role="alert">';
								echo "Brak zgłoszonych produkcji.";
								echo '</div>';
							}
							else
							{
								echo '<table class="table table-hover table-blue table-submissions">';
									echo '<thead class="thead-blue">';
										echo '<tr>';
											echo '<th class="th-id text-center" scope="col">ID</th>';
											echo '<th class="th-date text-center" scope="col">Data dodania</th>';
											echo '<th class="th-user text-center" scope="col">Użytkownik</th>';
											echo '<th class="th-media text-center" scope="col">Produkcja</th>';
											echo '<th class="th-type text-center" scope="col">Typ</th>';
										echo '</tr>';
									echo '</thead>';
									echo '<tbody>';
									while ($row = $result_media->fetch_assoc())
									{
										echo '<tr class="clickable-row" data-href="../submission_media/'.$row['id'].'/">';
											echo '<td class="text-center text-middle">'.$row['id'].'</td>';
											echo '<td class="text-center text-middle">'.$row['date'].'</td>';
											echo '<td class="text-center text-middle">'.$row['username'].'</td>';
											echo '<td class="text-center text-middle">'.$row['title'].'</td>';
											echo '<td class="text-center text-middle">';
											if($row['id_media'] == 0)
											{
												echo 'Nowy';
											}
											else
											{
												echo 'Edycja';
											}
											echo '</td>';
										echo '</tr>';
									}
									echo '</tbody>';
								echo '</table>';
							}
							?>
						</div>
						<div class="table-responsive">
							<?php
							$query_staff = "SELECT sm.id AS 'id', u.username AS 'username', sm.name AS 'name', sm.id_staff AS 'id_staff', sm.date_added AS 'date' FROM submissions_staff sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN staff s ON sm.id_staff = s.id WHERE sm.status=1 ORDER BY sm.id DESC";
							$result_staff = $mysqli->query($query_staff) or die($mysqli->error());
							if ($result_staff->num_rows == 0)
							{
								echo '<div class="alert alert-blue" role="alert">';
								echo "Brak zgłoszonej obsady.";
								echo '</div>';
							}
							else
							{
								echo '<table class="table table-hover table-blue table-submissions">';
									echo '<thead class="thead-blue">';
										echo '<tr>';
											echo '<th class="th-id text-center" scope="col">ID</th>';
											echo '<th class="th-date text-center" scope="col">Data dodania</th>';
											echo '<th class="th-user text-center" scope="col">Użytkownik</th>';
											echo '<th class="th-media text-center" scope="col">Obsada</th>';
											echo '<th class="th-type text-center" scope="col">Typ</th>';
										echo '</tr>';
									echo '</thead>';
									echo '<tbody>';
									while ($row = $result_staff->fetch_assoc())
									{
										echo '<tr class="clickable-row" data-href="../submission_staff/'.$row['id'].'/">';
											echo '<td class="text-center text-middle">'.$row['id'].'</td>';
											echo '<td class="text-center text-middle">'.$row['date'].'</td>';
											echo '<td class="text-center text-middle">'.$row['username'].'</td>';
											echo '<td class="text-center text-middle">'.$row['name'].'</td>';
											echo '<td class="text-center text-middle">';
											if($row['id_staff'] == 0)
											{
												echo 'Nowy';
											}
											else
											{
												echo 'Edycja';
											}
											echo '</td>';
										echo '</tr>';
									}
									echo '</tbody>';
								echo '</table>';
							}
							?>
						</div>
					</div>
					<div class="row submissions-accepted">
						<h4>Zaakceptowane</h4>
						<div class="table-responsive">
							<?php
							$query_media = "SELECT sm.id AS 'id', u.username AS 'username', sm.title AS 'title', sm.id_media AS 'id_media', sm.date_added AS 'date' FROM submissions_media sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN media m ON sm.id_media = m.id WHERE sm.status=2 ORDER BY sm.id DESC";
							$result_media = $mysqli->query($query_media) or die($mysqli->error());
							if ($result_media->num_rows == 0)
							{
								echo '<div class="alert alert-blue" role="alert">';
								echo "Brak zaakceptowanych produkcji.";
								echo '</div>';
							}
							else
							{
								echo '<table class="table table-hover table-blue table-submissions">';
									echo '<thead class="thead-blue">';
										echo '<tr>';
											echo '<th class="th-id text-center" scope="col">ID</th>';
											echo '<th class="th-date text-center" scope="col">Data dodania</th>';
											echo '<th class="th-user text-center" scope="col">Użytkownik</th>';
											echo '<th class="th-media text-center" scope="col">Produkcja</th>';
											echo '<th class="th-type text-center" scope="col">Typ</th>';
										echo '</tr>';
									echo '</thead>';
									echo '<tbody>';
									while ($row = $result_media->fetch_assoc())
									{
										echo '<tr class="clickable-row" data-href="../submission_media/'.$row['id'].'/">';
											echo '<td class="text-center text-middle">'.$row['id'].'</td>';
											echo '<td class="text-center text-middle">'.$row['date'].'</td>';
											echo '<td class="text-center text-middle">'.$row['username'].'</td>';
											echo '<td class="text-center text-middle">'.$row['title'].'</td>';
											echo '<td class="text-center text-middle">';
											if($row['id_media'] == 0)
											{
												echo 'Nowy';
											}
											else
											{
												echo 'Edycja';
											}
											echo '</td>';
										echo '</tr>';
									}
									echo '</tbody>';
								echo '</table>';
							}
							?>
						</div>
						<div class="table-responsive">
							<?php
							$query_staff = "SELECT sm.id AS 'id', u.username AS 'username', sm.name AS 'name', sm.id_staff AS 'id_staff', sm.date_added AS 'date' FROM submissions_staff sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN staff s ON sm.id_staff = s.id WHERE sm.status=2 ORDER BY sm.id DESC";
							$result_staff = $mysqli->query($query_staff) or die($mysqli->error());
							if ($result_staff->num_rows == 0)
							{
								echo '<div class="alert alert-blue" role="alert">';
								echo "Brak zaakceptowanej obsady.";
								echo '</div>';
							}
							else
							{
								echo '<table class="table table-hover table-blue table-submissions">';
									echo '<thead class="thead-blue">';
										echo '<tr>';
											echo '<th class="th-id text-center" scope="col">ID</th>';
											echo '<th class="th-date text-center" scope="col">Data dodania</th>';
											echo '<th class="th-user text-center" scope="col">Użytkownik</th>';
											echo '<th class="th-media text-center" scope="col">Obsada</th>';
											echo '<th class="th-type text-center" scope="col">Typ</th>';
										echo '</tr>';
									echo '</thead>';
									echo '<tbody>';
									while ($row = $result_staff->fetch_assoc())
									{
										echo '<tr class="clickable-row" data-href="../submission_staff/'.$row['id'].'/">';
											echo '<td class="text-center text-middle">'.$row['id'].'</td>';
											echo '<td class="text-center text-middle">'.$row['date'].'</td>';
											echo '<td class="text-center text-middle">'.$row['username'].'</td>';
											echo '<td class="text-center text-middle">'.$row['name'].'</td>';
											echo '<td class="text-center text-middle">';
											if($row['id_staff'] == 0)
											{
												echo 'Nowy';
											}
											else
											{
												echo 'Edycja';
											}
											echo '</td>';
										echo '</tr>';
									}
									echo '</tbody>';
								echo '</table>';
							}
							?>
						</div>
					</div>
					<div class="row submissions-rejected">
						<h4>Odrzucone</h4>
						<div class="table-responsive">
							<?php
							$query_media = "SELECT sm.id AS 'id', u.username AS 'username', sm.title AS 'title', sm.id_media AS 'id_media', sm.date_added AS 'date' FROM submissions_media sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN media m ON sm.id_media = m.id WHERE sm.status=3 ORDER BY sm.id DESC";
							$result_media = $mysqli->query($query_media) or die($mysqli->error());
							if ($result_media->num_rows == 0)
							{
								echo '<div class="alert alert-blue" role="alert">';
								echo "Brak odrzuconych produkcji.";
								echo '</div>';
							}
							else
							{
								echo '<table class="table table-hover table-blue table-submissions">';
									echo '<thead class="thead-blue">';
										echo '<tr>';
											echo '<th class="th-id text-center" scope="col">ID</th>';
											echo '<th class="th-date text-center" scope="col">Data dodania</th>';
											echo '<th class="th-user text-center" scope="col">Użytkownik</th>';
											echo '<th class="th-media text-center" scope="col">Produkcja</th>';
											echo '<th class="th-type text-center" scope="col">Typ</th>';
										echo '</tr>';
									echo '</thead>';
									echo '<tbody>';
									while ($row = $result_media->fetch_assoc())
									{
										echo '<tr class="clickable-row" data-href="../submission_media/'.$row['id'].'/">';
											echo '<td class="text-center text-middle">'.$row['id'].'</td>';
											echo '<td class="text-center text-middle">'.$row['date'].'</td>';
											echo '<td class="text-center text-middle">'.$row['username'].'</td>';
											echo '<td class="text-center text-middle">'.$row['title'].'</td>';
											echo '<td class="text-center text-middle">';
											if($row['id_media'] == 0)
											{
												echo 'Nowy';
											}
											else
											{
												echo 'Edycja';
											}
											echo '</td>';
										echo '</tr>';
									}
									echo '</tbody>';
								echo '</table>';
							}
							?>
						</div>
						<div class="table-responsive">
							<?php
							$query_staff = "SELECT sm.id AS 'id', u.username AS 'username', sm.name AS 'name', sm.id_staff AS 'id_staff', sm.date_added AS 'date' FROM submissions_staff sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN staff s ON sm.id_staff = s.id WHERE sm.status=3 ORDER BY sm.id DESC";
							$result_staff = $mysqli->query($query_staff) or die($mysqli->error());
							if ($result_staff->num_rows == 0)
							{
								echo '<div class="alert alert-blue" role="alert">';
								echo "Brak odrzuconej obsady.";
								echo '</div>';
							}
							else
							{
								echo '<table class="table table-hover table-blue table-submissions">';
									echo '<thead class="thead-blue">';
										echo '<tr>';
											echo '<th class="th-id text-center" scope="col">ID</th>';
											echo '<th class="th-date text-center" scope="col">Data dodania</th>';
											echo '<th class="th-user text-center" scope="col">Użytkownik</th>';
											echo '<th class="th-media text-center" scope="col">Obsada</th>';
											echo '<th class="th-type text-center" scope="col">Typ</th>';
										echo '</tr>';
									echo '</thead>';
									echo '<tbody>';
									while ($row = $result_staff->fetch_assoc())
									{
										echo '<tr class="clickable-row" data-href="../submission_staff/'.$row['id'].'/">';
											echo '<td class="text-center text-middle">'.$row['id'].'</td>';
											echo '<td class="text-center text-middle">'.$row['date'].'</td>';
											echo '<td class="text-center text-middle">'.$row['username'].'</td>';
											echo '<td class="text-center text-middle">'.$row['name'].'</td>';
											echo '<td class="text-center text-middle">';
											if($row['id_staff'] == 0)
											{
												echo 'Nowy';
											}
											else
											{
												echo 'Edycja';
											}
											echo '</td>';
										echo '</tr>';
									}
									echo '</tbody>';
								echo '</table>';
							}
							?>
						</div>
					</div>
				</div>
			</main>
			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script>
			$('[href*="../submissions/"]').addClass('active');
			
			$(".clickable-row").click(function() {
				window.location = $(this).data("href");
			});
		</script>
	</body>
</html>