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
							$result = $mysqli->query("SELECT * FROM submissions_media WHERE id='$id'") or die($mysqli->error());
							if ($result->num_rows != 0)
							{
								$media_edit = $result->fetch_assoc();
								$id = $media_edit['id'];
								$id_user = $media_edit['id_user'];
								$id_media = $media_edit['id_media'];
								$title = $media_edit['title'];
								$description = $media_edit['description'];
								$poster = $media_edit['poster'];
								$type = $media_edit['type'];
								$premiere = $media_edit['premiere'];
								$episodes = $media_edit['episodes'];
								$duration = $media_edit['duration'];
								$genre = explode("|",$media_edit['genre']);
								$status = $media_edit['status'];
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
					
					$result = $mysqli->query("SELECT relations, relations_types FROM submissions_media WHERE id='$id'") or die($mysqli->error());
					if ($result->num_rows != 0)
					{
						$relation_result = $result->fetch_assoc();
						
						$relations = explode('|', $relation_result['relations']);
						
						$relations_types = explode('|', $relation_result['relations_types']);
					}
					
					$result = $mysqli->query("SELECT staffs, staffs_types, staffs_as FROM submissions_media WHERE id='$id'") or die($mysqli->error());
					if ($result->num_rows != 0)
					{
						$staff_result = $result->fetch_assoc();
						
						$staffs = explode('|', $staff_result['staffs']);
						
						$staffs_types = explode('|', $staff_result['staffs_types']);
						
						$staffs_as = explode('|', $staff_result['staffs_as']);
					}
					?>
					<form id="submission-media-form" enctype="multipart/form-data">
						<h1 class="h3 mb-3 font-weight-normal">
						<?php
						if($id_media == 0)
						{
							echo 'Dodawanie nowej produkcji';
						}
						else
						{
							echo 'Edycja produkcji';
						}
						?>
						</h1>
						<div class="form-group">
							<label for="user">Użytkownik</label>
							<input type="text" class="form-control" id="user" name="user" value="<?php echo 'ID: '.$user['id'].' - Nazwa: '.$user['username'].' - Dodanych: '.$user_submissions['sum']; ?>" disabled>
						</div>
						<div class="form-group">
							<label for="title">Tytuł</label>
							<input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" readonly="readonly">
							<small id="title-help" class="form-text text-muted">
								Tytuł produkcji (w języku polskim jeśli dostępny).
							</small>
						</div>
						<div class="form-group">
							<label for="poster">Plakat</label>
							<div class="container form-container-file submission">
								<img src="
								<?php
								if(empty($media_edit['poster']))
									echo '../files/images/default.png';
								else
									echo '../media_edit/'.$media_edit['poster'];
								?>
								" id="poster-previev" class="poster-mini" >
							</div>
						</div>
						<div class="form-group">
							<label for="description">Opis</label>
							<textarea class="form-control" id="description" name="description" rows="3" readonly="readonly"><?php echo $description; ?></textarea>
						</div>
						<div class="form-group">
							<label for="type">Typ</label>
							<select class="form-control" id="type" name="type" readonly="readonly">
								<option value="0" <?php if($type == '0'){echo 'selected';}?>>Wybierz typ...</option>
								<option value="1" <?php if($type == '1'){echo 'selected';}?>>Film fabularny</option>
								<option value="2" <?php if($type == '2'){echo 'selected';}?>>Film dokumentalny</option>
								<option value="3" <?php if($type == '3'){echo 'selected';}?>>Film animowany</option>
								<option value="4" <?php if($type == '4'){echo 'selected';}?>>Serial fabularny</option>
								<option value="5" <?php if($type == '5'){echo 'selected';}?>>Serial dokumentalny</option>
								<option value="6" <?php if($type == '6'){echo 'selected';}?>>Serial animowany</option>
								<option value="7" <?php if($type == '7'){echo 'selected';}?>>Inny</option>
							</select>
						</div>
						<div class="form-group">
							<label for="premiere">Data premiery</label>
							<input type="date" class="form-control" id="premiere" name="premiere" value="<?php echo $premiere; ?>" readonly="readonly">
						</div>
						<div class="form-group">
							<label for="episodes">Ilość odcinków *</label>
							<input type="number" class="form-control" id="episodes" name="episodes" value="<?php echo $episodes; ?>" readonly="readonly">
							<small id="episodes-help" class="form-text text-muted">
								Ilość odcinków składających sie na tytuł (1 dla filmu).
							</small>
						</div>
						<div class="form-group">
							<label for="duration">Czas trwania</label>
							<input type="number" class="form-control" id="duration" name="duration" value="<?php echo $duration; ?>" readonly="readonly">
							<small id="duration-help" class="form-text text-muted">
								Czas trwania filmu lub pojedynczego odcinka w minutach.
							</small>
						</div>
						<div class="form-group">
							<label for="genre">Gatunek</label></br>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre1" name="genre1" <?php if (in_array("1", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre1">Akcja</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre2" name="genre2" <?php if (in_array("2", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre2">Przygodowy</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre3" name="genre3" <?php if (in_array("3", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre3">Komedia</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre4" name="genre4" <?php if (in_array("4", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre4">Dramat</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre5" name="genre5" <?php if (in_array("5", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre5">Fantastyka</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre6" name="genre6" <?php if (in_array("6", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre6">Horror</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre7" name="genre7" <?php if (in_array("7", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre7">Zagadka</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre8" name="genre8" <?php if (in_array("8", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre8">Psychologiczny</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre9" name="genre9" <?php if (in_array("9", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre9">Romans</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre10" name="genre10" <?php if (in_array("10", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre10">Sci-Fi</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre11" name="genre11" <?php if (in_array("11", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre11">Okruchy życia</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre12" name="genre12" <?php if (in_array("12", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre12">Sportowy</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre13" name="genre13" <?php if (in_array("13", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre13">Nadprzyrodzone</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre14" name="genre14" <?php if (in_array("14", $genre)){echo 'checked'; } ?> readonly="readonly">
								<label class="form-check-label" for="genre14">Thriller</label>
							</div>
						</div>
						<div id="relations-container">
							<label for="relations-container">Relacje</label>
							<?php
							$media_count = 0;
							if(empty($relations[0]))
							{
								$media_count++;
								echo '<div class="alert alert-blue" role="alert">';
								echo "Brak relacji.";
								echo '</div>';
							}
							else
							{
								for($x = 0; $x < count($relations); $x++)
								{
									$media_count++;
									echo '<div class="form-group">';
										echo '<input type="text" class="form-control form-control-top" id="relation'.$media_count.'" name="relation'.$media_count.'" value="'.$relations[$x].'" placeholder="Tytuł powiązanej produkcji..." readonly="readonly">';
										echo '<select class="form-control form-control-bottom submission" id="relation'.$media_count.'_type" name="relation'.$media_count.'_type" readonly="readonly">';
											echo '<option value="0"';
											if($relations_types[$x] == 0) {echo ' selected';}
											echo '>Wybierz typ powiązania...</option>';
											echo '<option value="1"';
											if($relations_types[$x] == 1) {echo ' selected';}
											echo '>Sequel</option>';
											echo '<option value="2"';
											if($relations_types[$x] == 2) {echo ' selected';}
											echo '>Prequel</option>';
											echo '<option value="3"';
											if($relations_types[$x] == 3) {echo ' selected';}
											echo '>Spin-off</option>';
											echo '<option value="4"';
											if($relations_types[$x] == 4) {echo ' selected';}
											echo '>Inny</option>';
										echo '</select>';
									echo '</div>';
								}
							}
							?>
						</div>
						<div id="staff-container">
							<label for="staff-container">Obsada</label>
							<?php
							$staff_count = 0;
							if(empty($staffs[0]))
							{
								$staff_count++;
								echo '<div class="alert alert-blue" role="alert">';
								echo "Brak obsady.";
								echo '</div>';
							}
							else
							{
								for($x = 0; $x < count($staffs); $x++)
								{
									$staff_count++;
									echo '<div class="form-group">';
										echo '<input type="text" class="form-control form-control-top" id="staff'.$staff_count.'" name="staff'.$staff_count.'" value="'.$staffs[$x].'" readonly="readonly">';
										echo '<select class="form-control form-control-bottom select-staff-type submission';
										if($staffs_types[$x] == 1 || $staffs_types[$x] == 2){echo ' form-control-middle';}
										echo '" id="staff'.$staff_count.'_type" name="staff'.$staff_count.'_type" readonly="readonly">';
											echo '<option value="0"';
											if($staffs_types[$x] == 0) {echo ' selected';}
											echo '>Wybierz funkcję...</option>';
											echo '<option value="1"';
											if($staffs_types[$x] == 1) {echo ' selected';}
											echo '>Główny bohater</option>';
											echo '<option value="2"';
											if($staffs_types[$x] == 2) {echo ' selected';}
											echo '>Postać poboczna</option>';
											echo '<option value="3"';
											if($staffs_types[$x] == 3) {echo ' selected';}
											echo '>Reżyseria</option>';
											echo '<option value="4"';
											if($staffs_types[$x] == 4) {echo ' selected';}
											echo '>Scenariusz</option>';
											echo '<option value="5"';
											if($staffs_types[$x] == 5) {echo ' selected';}
											echo '>Zdjęcia</option>';
											echo '<option value="6"';
											if($staffs_types[$x] == 6) {echo ' selected';}
											echo '>Muzyka</option>';
											echo '<option value="7"';
											if($staffs_types[$x] == 7) {echo ' selected';}
											echo '>Produkcja</option>';
											echo '<option value="8"';
											if($staffs_types[$x] == 8) {echo ' selected';}
											echo '>Inny</option>';
										echo '</select>';
										echo '<input type="text" class="form-control form-control-bottom submission';
										if($staffs_types[$x] != 1 && $staffs_types[$x] != 2){echo ' input-staff-as';}
										echo '" id="staff'.$staff_count.'_as" name="staff'.$staff_count.'_as" placeholder="Zagrał jako..." value="';
										if(isset($staffs_as[$x])){echo $staffs_as[$x];}
										echo '" readonly="readonly">';
									echo '</div>';
								}
							}
							?>
						</div>
						<div class="form-group form-hidden">
							<input type="text" class="form-control" id="id-submission" value="<?php echo $id;?>" name="id_submission">
							<input type="text" class="form-control" id="id-user" value="<?php echo $id_user;?>" name="id_user">
							<input type="text" class="form-control" id="id-media" value="<?php echo $id_media;?>" name="id_media">
							<input type="text" class="form-control" id="poster" value="<?php echo $poster;?>" name="poster">
							<input type="text" class="form-control" id="relations-count" value="<?php echo $media_count; ?>" name="relations_count">
							<input type="text" class="form-control" id="staff-count" value="<?php echo $staff_count; ?>" name="staff_count">
						</div>
						<?php
						if($status == '1')
						{
							echo '<div class="form-row">';
								echo '<div class="col">';
									echo '<button type="submit" id="submission-media-decline" class="btn btn-danger btn-block" name="submit">Odrzuć</button>';
								echo '</div>';
								echo '<div class="col">';
									echo '<button type="submit" id="submission-media-accept" class="btn btn-success btn-block" name="submit">Akceptuj</button>';
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
				$('.select-staff-type').change(function(){
				   var selected = $(this).find('option:selected').attr('value');
				   if(selected == '1' || selected == '2')
				   {
					   $(this).addClass('form-control-middle');
					   $(this).next().show();
				   }
				   else
				   {
					   $(this).removeClass('form-control-middle');
					   $(this).next().hide();
				   }
				});
				
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