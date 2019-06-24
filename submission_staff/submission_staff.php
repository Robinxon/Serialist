<?php
require("../components/database.php");
session_start();

if(!isset($_SESSION['logged']) || !$_SESSION['logged'])
{
	$_SESSION['message'] = "Musisz się zalogować, aby przejść na tę stronę!";
	$_SESSION['message_type'] = "danger";
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
<!DOCTYPE html>
<html lang="pl">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<base href='http://localhost/'>

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
					<?php
					if($_SERVER['REQUEST_METHOD'] == 'GET')
					{
						if(isset($_GET['id']) && !empty($_GET['id']))
						{
							$id = $_GET['id'];
							$result = $mysqli->query("SELECT * FROM submissions_staff WHERE id='$id'") or die($mysqli->error());
							if ($result->num_rows != 0)
							{
								$staff_edit = $result->fetch_assoc();
								$id = $staff_edit['id'];
								$id_user = $staff_edit['id_user'];
								$id_staff = $staff_edit['id_staff'];
								$name = $staff_edit['name'];
								$description = $staff_edit['description'];
								$photo = $staff_edit['photo'];
								$status = $staff_edit['status'];
							}
							else
							{
								$_SESSION['message'] = "Brak zawartości o takim identyfikatorze!";
								$_SESSION['message_type'] = "warning";
								session_write_close();
								header("Location: http://localhost/submissions/");
							}
						}
						else
						{
							$_SESSION['message'] = "Brak zawartości o takim identyfikatorze!";
							$_SESSION['message_type'] = "warning";
							session_write_close();
							header("Location: http://localhost/submissions/");
						}
					}
					else
					{
						$_SESSION['message'] = "Brak zawartości o takim identyfikatorze!";
						$_SESSION['message_type'] = "warning";
						session_write_close();
						header("Location: http://localhost/submissions/");
					}
					
					$result = $mysqli->query("SELECT * FROM users WHERE id='$id_user'") or die($mysqli->error());
					if ($result->num_rows != 0)
					{
						$user = $result->fetch_assoc();
					}
					
					$result = $mysqli->query("SELECT (SELECT COUNT(*) FROM submissions_media WHERE id_user='$id_user') + (SELECT COUNT(*) FROM submissions_staff WHERE id_user='$id_user') AS sum") or die($mysqli->error());
					$user_submissions = $result->fetch_assoc();
					?>
					<form id="submission-staff-form" enctype="multipart/form-data">
						<h1 class="h3 mb-3 font-weight-normal">
						<?php
						if($id_staff == 0)
						{
							echo 'Dodawanie nowej obsady';
						}
						else
						{
							echo 'Edycja obsady';
						}
						?>
						</h1>
						<div class="form-group">
							<label for="user">Użytkownik</label>
							<input type="text" class="form-control" id="user" name="user" value="<?php echo 'ID: '.$user['id'].' - Nazwa: '.$user['username'].' - Dodanych: '.$user_submissions['sum']; ?>" readonly="readonly">
						</div>
						<div class="form-group">
							<label for="name">Imię i nazwisko</label>
							<input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" readonly="readonly">
							<small id="name-help" class="form-text text-muted">
								Imię i nazwisko lub pseudonim artystyczny osoby.
							</small>
						</div>
						<div class="form-group">
							<label for="poster">Photo</label>
							<div class="container form-container-file submission">
								<img src="
								<?php
								if(empty($staff_edit['photo']))
									echo '../files/images/default.png';
								else
									echo '../staff_edit/'.$staff_edit['photo'];
								?>
								" id="poster-previev" class="poster-mini" >
							</div>
						</div>
						<div class="form-group">
							<label for="description">Opis</label>
							<textarea class="form-control" id="description" name="description" rows="3" readonly="readonly"><?php echo $description; ?></textarea>
						</div>
						<div class="form-group form-hidden">
							<input type="text" class="form-control" id="id-submission" value="<?php echo $id;?>" name="id_submission">
							<input type="text" class="form-control" id="id-user" value="<?php echo $id_user;?>" name="id_user">
							<input type="text" class="form-control" id="id-staff" value="<?php echo $id_staff;?>" name="id_staff">
							<input type="text" class="form-control" id="photo" value="<?php echo $photo;?>" name="photo">
						</div>
						<?php
						if($status == '1')
						{
							echo '<div class="form-row">';
								echo '<div class="col">';
									echo '<button type="submit" id="submission-staff-decline" class="btn btn-danger btn-block" name="submit">Odrzuć</button>';
								echo '</div>';
								echo '<div class="col">';
									echo '<button type="submit" id="submission-staff-accept" class="btn btn-success btn-block" name="submit">Akceptuj</button>';
								echo '</div>';
							echo '</div>';
						}
						?>
					</form>
				</div>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script src="../js/user_actions.js"></script>
		<script>
			$(document).ready(function() {			
				$(function () {
					$("#poster").change(function () {
						readURL(this);
					});
				});
				
				function readURL(input) {
					if (input.files && input.files[0]) {
						var reader = new FileReader();

						reader.onload = function (e) {
							//alert(e.target.result);
							$('#poster-previev').attr('src', e.target.result);
						}

						reader.readAsDataURL(input.files[0]);
					}
				}
			});
		</script>
	</body>
</html>