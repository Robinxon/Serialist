<?php
require("../components/database.php");
session_start();

if(!isset($_SESSION['logged']) || !$_SESSION['logged'])
{
	$_SESSION['message'] = "Musisz się zalogować, aby przejść na tę stronę!";
	$_SESSION['message_type'] = "danger";
	header("Location: http://localhost/home/");
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

		<title>Serialist</title>
	</head>
	<body>
		<div id="wrapper" class="d-flex w-100 h-100 p-3 mx-auto flex-column">
			<?php require("../components/header.php") ?>

			<main role="main" class="inner cover">
				<div class="container">
					<?php require("../components/alerts.php") ?>
					<div class="row">
						<div class="col col-sm-3">
							<ul class="list-group">
								<a href="#" onClick="click_user()" id="select-user" class="list-group-item list-group-item-action active">Użytkownik</a>
								<a href="#" onClick="click_submissions()" id="select-submissions" class="list-group-item list-group-item-action">Zgłoszenia</a>
							</ul>
						</div>
						<div id="option-user" class="col-sm-9">
							<h1>Zmień dane konta</h1>
							<?php
							$id_user = $_SESSION['id_user'];
							$query = "SELECT * FROM users WHERE id = '$id_user'";
							$result = $mysqli->query($query) or die($mysqli->error());
							$user = $result->fetch_assoc();
							?>
							<form id="user-settings" action="" method="post">
								<div class="form-group">
									<label for="usename">Nazwa użytkownika</label>
									<input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username'] ?>">
								</div>
								<div class="form-group">
									<label for="email">Adres email</label>
									<input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email'] ?>">
								</div>
								<div class="form-group">
									<label for="new-password">Nowe hasło</label>
									<input type="password" class="form-control" id="new-password" name="new_password">
								</div>
								<div class="form-group">
									<label for="repeat-password">Powtórz hasło</label>
									<input type="password" class="form-control" id="repeat-password" name="repeat_password">
								</div>
								<div class="form-group">
									<label for="avatar">Awatar</label>
									<div class="container form-container-file">
										<?php
										if(empty($user['avatar']))
											echo '<img src="../files/images/avatar/u0-default.png" id="avatar-previev" class="avatar-mini" />';
										else
											echo '<img src="../files/images/avatar/'.$user['avatar'].'" id="avatar-previev" class="avatar-mini" />';
										?>
										<input type="file" class="form-control-file form-container-file-input" id="avatar" name="avatar">
									</div>
								</div>
								<button type="submit" id="send-settings" class="btn btn-primary">Zapisz zmiany</button>
							</form>
						</div>
						<div id="option-submissions" class="col-sm-9" style="display: none;">
							<h1>Zgłoszenia <a role="button" class="btn btn-primary" href="../media_edit/">Zgłoś nowe media</a> <a role="button" class="btn btn-primary" href="../staff_edit/">Zgłoś nową obsadę</a></h1>
							<h4>Nowe</h4>
							<div class="table-responsive">
								<?php
								$id_user = $_SESSION['id_user'];
								$query_media = "SELECT sm.id as 'id', u.username as 'username', sm.title as 'title', sm.id_media as 'id_media', sm.date_added as 'date' FROM submissions_media sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN media m ON sm.id_media = m.id WHERE sm.id_user='$id_user' AND sm.status='1' ORDER BY sm.id DESC";
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
								$query_staff = "SELECT sm.id AS 'id', u.username AS 'username', sm.name AS 'name', sm.id_staff AS 'id_staff', sm.date_added AS 'date' FROM submissions_staff sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN staff s ON sm.id_staff = s.id WHERE sm.id_user='$id_user' AND sm.status=1 ORDER BY sm.id DESC";
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
							<h4>Zaakceptowane</h4>
							<div class="table-responsive">
								<?php
								$id_user = $_SESSION['id_user'];
								$query_media = "SELECT sm.id as 'id', u.username as 'username', sm.title as 'title', sm.id_media as 'id_media', sm.date_added as 'date' FROM submissions_media sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN media m ON sm.id_media = m.id WHERE sm.id_user='$id_user' AND sm.status='2' ORDER BY sm.id DESC";
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
								$query_staff = "SELECT sm.id AS 'id', u.username AS 'username', sm.name AS 'name', sm.id_staff AS 'id_staff', sm.date_added AS 'date' FROM submissions_staff sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN staff s ON sm.id_staff = s.id WHERE sm.id_user='$id_user' AND sm.status=2 ORDER BY sm.id DESC";
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
							<h4>Odrzucone</h4>
							<div class="table-responsive">
								<?php
								$id_user = $_SESSION['id_user'];
								$query_media = "SELECT sm.id as 'id', u.username as 'username', sm.title as 'title', sm.id_media as 'id_media', sm.date_added as 'date' FROM submissions_media sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN media m ON sm.id_media = m.id WHERE sm.id_user='$id_user' AND sm.status='3' ORDER BY sm.id DESC";
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
								$query_staff = "SELECT sm.id AS 'id', u.username AS 'username', sm.name AS 'name', sm.id_staff AS 'id_staff', sm.date_added AS 'date' FROM submissions_staff sm LEFT JOIN users u ON sm.id_user = u.id LEFT JOIN staff s ON sm.id_staff = s.id WHERE sm.id_user='$id_user' AND sm.status='3' ORDER BY sm.id DESC";
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
				</div>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script src="../js/user_actions.js"></script>
		<script>
			$('[href*="../settings/"]').addClass('active');
			function click_user() {
				$('#option-submissions').hide();
				$('#select-submissions').removeClass('active');
				
				$('#option-user').show();
				$('#select-user').addClass('active');
			}
			function click_submissions() {
				$('#option-user').hide();
				$('#select-user').removeClass('active');;
				
				$('#option-submissions').show();
				$('#select-submissions').addClass('active');
			}
			
			$(function () {
				$("#avatar").change(function () {
					readURL(this);
				});
			});
				
			function readURL(input) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();

					reader.onload = function (e) {
						//alert(e.target.result);
						$('#avatar-previev').attr('src', e.target.result);
					}

					reader.readAsDataURL(input.files[0]);
				}
			}
		</script>
	</body>
</html>