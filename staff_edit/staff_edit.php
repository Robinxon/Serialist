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
					function noValidMedia() {
						global $id, $name, $description, $photo;
						$id = 0;
						$name = '';
						$description = '';
						$photo = '../files/images/default.png';
					}
					
					if($_SERVER['REQUEST_METHOD'] == 'GET')
					{
						if(isset($_GET['id']) && !empty($_GET['id']))
						{
							$temp_id = $_GET['id'];
							$result = $mysqli->query("SELECT * FROM staff WHERE id='$temp_id'") or die($mysqli->error());
							if ($result->num_rows != 0)
							{
								$staff_edit = $result->fetch_assoc();
								$id = $staff_edit['id'];
								$name = $staff_edit['name'];
								$description = $staff_edit['description'];
								$photo = '../files/images/photo/'.$staff_edit['photo'];
							}
							else
							{
								noValidMedia();
							}
						}
						else
						{
							noValidMedia();
						}
					}
					else
					{
						noValidMedia();
					}
					?>
					<form id="staff-edit-form" action="../staff_edit/test.php" method="POST" enctype="multipart/form-data">
						<h1 class="h3 mb-3 font-weight-normal">
						<?php
						if($id == 0)
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
							<label for="name">Imię i nazwisko *</label>
							<input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
							<small id="name-help" class="form-text text-muted">
								Imię i nazwisko lub pseudonim artystyczny osoby.
							</small>
						</div>
						<div class="form-group">
							<label for="photo">Zdjęcie</label>
							<div class="container form-container-file">
								<img src="<?php echo $photo; ?>" id="photo-previev" class="poster-mini" >
								<input type="file" accept="image/*" class="form-control-file form-container-file-input" id="photo" name="photo">
							</div>
						</div>
						<div class="form-group">
							<label for="description">Opis</label>
							<textarea class="form-control" id="description" name="description" rows="3"><?php echo $description; ?></textarea>
						</div>
						<div class="form-group form-hidden">
							<input type="text" class="form-control" id="id_user" value="<?php echo $_SESSION['id_user'];?>" name="id_user">
							<input type="text" class="form-control" id="hash" value="<?php echo $_SESSION['hash'];?>" name="hash">
							<input type="text" class="form-control" id="id_staff" value="<?php echo $id;?>" name="id_staff">
						</div>
						<div class="form-group">
							<button type="submit" id="staff-send" class="btn btn-primary btn-block" name="submit">Wyślij</button>
							<small id="submit-help" class="form-text text-muted">
								* Pola oznaczone gwiazdką są wymagane!
							</small>
						</div>
					</form>
				</div>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script src="../js/user_actions.js"></script>
		<script>
			$(function () {
				$("#photo").change(function () {
					readURL(this);
				});
			});
			
			function readURL(input) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();

					reader.onload = function (e) {
						//alert(e.target.result);
						$('#photo-previev').attr('src', e.target.result);
					}

					reader.readAsDataURL(input.files[0]);
				}
			}
		</script>
	</body>
</html>