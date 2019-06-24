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
						global $id, $title, $description, $poster, $type, $premiere, $episodes, $duration, $genre;
						$id = 0;
						$title = '';
						$description = '';
						$poster = '../files/images/default.png';
						$type = 0;
						$premiere = '';
						$episodes = '';
						$duration = '';
						$genre = array();
					}
					
					if($_SERVER['REQUEST_METHOD'] == 'GET')
					{
						if(isset($_GET['id']) && !empty($_GET['id']))
						{
							$temp_id = $_GET['id'];
							$result = $mysqli->query("SELECT * FROM media WHERE id='$temp_id'") or die($mysqli->error());
							if ($result->num_rows != 0)
							{
								$media_edit = $result->fetch_assoc();
								$id = $media_edit['id'];
								$title = $media_edit['title'];
								$description = $media_edit['description'];
								$poster = '../files/images/cover-large/'.$media_edit['poster'];
								$type = $media_edit['type'];
								$premiere = $media_edit['premiere'];
								$episodes = $media_edit['episodes'];
								$duration = $media_edit['duration'];
								$genre = explode("|",$media_edit['genre']);
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
					
					$result = $mysqli->query("SELECT * FROM media") or die($mysqli->error());
					$media = [];
					while($row = mysqli_fetch_array($result))
					{
						$media[] = $row;
					}
					
					$result = $mysqli->query("SELECT * FROM staff") or die($mysqli->error());
					$staff = [];
					while($row = mysqli_fetch_array($result))
					{
						$staff[] = $row;
					}
					
					$result = $mysqli->query("SELECT media.id, media.title, related_media.type FROM related_media LEFT JOIN media ON related_media.id_2 = media.id WHERE id_1='$id'") or die($mysqli->error());
					if ($result->num_rows != 0)
					{
						$related_media = array();
						while($line = $result->fetch_assoc()){
							$related_media[] = $line;
						}
					}
					else
					{
						$related_media = '';
					}
					
					$result = $mysqli->query("SELECT staff.id, staff.name, related_staff.type, related_staff.play_as FROM related_staff LEFT JOIN staff ON related_staff.id_2 = staff.id WHERE id_1='$id'") or die($mysqli->error());
					if ($result->num_rows != 0)
					{
						$related_staff = array();
						while($line = $result->fetch_assoc()){
							$related_staff[] = $line;
						}
					}
					else
					{
						$related_staff = '';
					}
					?>
					<form id="media-edit-form" action="../media_edit/test.php" method="POST" enctype="multipart/form-data">
						<h1 class="h3 mb-3 font-weight-normal">
						<?php
						if($id == 0)
						{
							echo 'Dodawanie nowej produkcji';
						}
						else
						{
							echo 'Edycja produkcji';
						}
						?>
						</h1>
						<small id="title-help" class="form-text text-muted">
							Przed dodaniem obsady upewnij się, że najpierw do bazy została dodana cała obsada. Możesz to zrobić <a href="../staff_edit/">tutaj</a>.
						</small>
						<div class="form-group">
							<label for="title">Tytuł *</label>
							<input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" required>
							<small id="title-help" class="form-text text-muted">
								Tytuł produkcji (w języku polskim jeśli dostępny).
							</small>
						</div>
						<div class="form-group">
							<label for="poster">Plakat</label>
							<div class="container form-container-file">
								<img src="<?php echo $poster; ?>" id="poster-previev" class="poster-mini" >
								<input type="file" accept="image/*" class="form-control-file form-container-file-input" id="poster" name="poster">
							</div>
						</div>
						<div class="form-group">
							<label for="description">Opis</label>
							<textarea class="form-control" id="description" name="description" rows="3"><?php echo $description; ?></textarea>
						</div>
						<div class="form-group">
							<label for="type">Typ *</label>
							<select class="form-control" id="type" name="type">
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
							<input type="date" class="form-control" id="premiere" name="premiere" value="<?php echo $premiere; ?>">
						</div>
						<div class="form-group">
							<label for="episodes">Ilość odcinków *</label>
							<input type="number" class="form-control" id="episodes" name="episodes" value="<?php echo $episodes; ?>" required>
							<small id="episodes-help" class="form-text text-muted">
								Ilość odcinków składających sie na tytuł (1 dla filmu).
							</small>
						</div>
						<div class="form-group">
							<label for="duration">Czas trwania</label>
							<input type="number" class="form-control" id="duration" name="duration" value="<?php echo $duration; ?>">
							<small id="duration-help" class="form-text text-muted">
								Czas trwania filmu lub pojedynczego odcinka w minutach.
							</small>
						</div>
						<div class="form-group">
							<label for="genre">Gatunek</label></br>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre1" name="genre1" <?php if (in_array("1", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre1">Akcja</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre2" name="genre2" <?php if (in_array("2", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre2">Przygodowy</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre3" name="genre3" <?php if (in_array("3", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre3">Komedia</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre4" name="genre4" <?php if (in_array("4", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre4">Dramat</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre5" name="genre5" <?php if (in_array("5", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre5">Fantastyka</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre6" name="genre6" <?php if (in_array("6", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre6">Horror</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre7" name="genre7" <?php if (in_array("7", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre7">Zagadka</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre8" name="genre8" <?php if (in_array("8", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre8">Psychologiczny</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre9" name="genre9" <?php if (in_array("9", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre9">Romans</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre10" name="genre10" <?php if (in_array("10", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre10">Sci-Fi</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre11" name="genre11" <?php if (in_array("11", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre11">Okruchy życia</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre12" name="genre12" <?php if (in_array("12", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre12">Sportowy</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre13" name="genre13" <?php if (in_array("13", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre13">Nadprzyrodzone</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" id="genre14" name="genre14" <?php if (in_array("14", $genre)){echo 'checked'; } ?>>
								<label class="form-check-label" for="genre14">Thriller</label>
							</div>
						</div>
						<div id="relations-container">
							<label for="relations-container">Relacje</label>
							<?php
							$media_count = 0;
							if(empty($related_media))
							{
								$media_count++;
								echo '<div class="form-group">';
									echo '<input type="text" list="media_list" class="form-control form-control-top" id="relation1" name="relation1" placeholder="Tytuł powiązanej produkcji...">';
									echo '<datalist id="media_list">';
										foreach ($media as $row)
											echo '<option value="'.$row['title'].'">'.$row['title'].'</option>';
									echo '</datalist>';
									echo '<select class="form-control form-control-bottom" id="relation1_type" name="relation1_type">';
										echo '<option value="0">Wybierz typ powiązania...</option>';
										echo '<option value="1">Sequel</option>';
										echo '<option value="2">Prequel</option>';
										echo '<option value="3">Spin-off</option>';
										echo '<option value="4">Inny</option>';
									echo '</select>';
									echo '<button type="button" class="btn btn-danger btn-sm delete-relation">Usuń relację</button>';
								echo '</div>';
							}
							else
							{
								foreach($related_media as $related)
								{
									$media_count++;
									echo '<div class="form-group">';
										echo '<input type="text" list="media_list" class="form-control form-control-top" id="relation'.$media_count.'" name="relation'.$media_count.'" value="'.$related['title'].'" placeholder="Tytuł powiązanej produkcji...">';
										echo '<datalist id="media_list">';
											foreach ($media as $row)
												echo '<option value="'.$row['title'].'">'.$row['title'].'</option>';
										echo '</datalist>';
										echo '<select class="form-control form-control-bottom" id="relation'.$media_count.'_type" name="relation'.$media_count.'_type">';
											echo '<option value="0"';
											if($related['type'] == 0) {echo ' selected';}
											echo '>Wybierz typ powiązania...</option>';
											echo '<option value="1"';
											if($related['type'] == 1) {echo ' selected';}
											echo '>Sequel</option>';
											echo '<option value="2"';
											if($related['type'] == 2) {echo ' selected';}
											echo '>Prequel</option>';
											echo '<option value="3"';
											if($related['type'] == 3) {echo ' selected';}
											echo '>Spin-off</option>';
											echo '<option value="4"';
											if($related['type'] == 4) {echo ' selected';}
											echo '>Inny</option>';
										echo '</select>';
										echo '<button type="button" class="btn btn-danger btn-sm delete-relation">Usuń relację</button>';
									echo '</div>';
								}
							}
							?>
						</div>
						<button type="button" id="add-relation" class="btn btn-secondary">Dodaj relację</button>
						<div id="staff-container">
							<label for="staff-container">Obsada</label>
							<?php
							$staff_count = 0;
							if(empty($related_staff))
							{
								$staff_count++;
								echo '<div class="form-group">';
									echo '<input type="text" list="staff_list" class="form-control form-control-top" id="staff1" name="staff1">';
									echo '<datalist id="staff_list">';
										foreach ($staff as $row)
											echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';
									echo '</datalist>';
									echo '<select class="form-control form-control-bottom select-staff-type" id="staff1_type" name="staff1_type">';
										echo '<option value="0">Wybierz funkcję...</option>';
										echo '<option value="1">Główny bohater</option>';
										echo '<option value="2">Postać poboczna</option>';
										echo '<option value="3">Reżyseria</option>';
										echo '<option value="4">Scenariusz</option>';
										echo '<option value="5">Zdjęcia</option>';
										echo '<option value="6">Muzyka</option>';
										echo '<option value="7">Produkcja</option>';
										echo '<option value="8">Inny</option>';
									echo '</select>';
									echo '<input type="text" class="form-control form-control-bottom input-staff-as" id="staff1_as" name="staff1_as" placeholder="Zagrał jako...">';
									echo '<button type="button" class="btn btn-danger btn-sm delete-staff">Usuń obsadę</button>';
								echo '</div>';
							}
							else
							{
								foreach($related_staff as $related)
								{
									$staff_count++;
									echo '<div class="form-group">';
										echo '<input type="text" list="staff_list" class="form-control form-control-top" id="staff'.$staff_count.'" name="staff'.$staff_count.'" value="'.$related['name'].'">';
										echo '<datalist id="staff_list">';
											foreach ($staff as $row)
												echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';
										echo '</datalist>';
										echo '<select class="form-control form-control-bottom select-staff-type';
										if($related['type'] == 1 || $related['type'] == 2){echo ' form-control-middle';}
										echo '" id="staff'.$staff_count.'_type" name="staff'.$staff_count.'_type">';
											echo '<option value="0"';
											if($related['type'] == 0) {echo ' selected';}
											echo '>Wybierz funkcję...</option>';
											echo '<option value="1"';
											if($related['type'] == 1) {echo ' selected';}
											echo '>Główny bohater</option>';
											echo '<option value="2"';
											if($related['type'] == 2) {echo ' selected';}
											echo '>Postać poboczna</option>';
											echo '<option value="3"';
											if($related['type'] == 3) {echo ' selected';}
											echo '>Reżyseria</option>';
											echo '<option value="4"';
											if($related['type'] == 4) {echo ' selected';}
											echo '>Scenariusz</option>';
											echo '<option value="5"';
											if($related['type'] == 5) {echo ' selected';}
											echo '>Zdjęcia</option>';
											echo '<option value="6"';
											if($related['type'] == 6) {echo ' selected';}
											echo '>Muzyka</option>';
											echo '<option value="7"';
											if($related['type'] == 7) {echo ' selected';}
											echo '>Produkcja</option>';
											echo '<option value="8"';
											if($related['type'] == 8) {echo ' selected';}
											echo '>Inny</option>';
										echo '</select>';
										echo '<input type="text" class="form-control form-control-bottom';
										if($related['type'] != 1 && $related['type'] != 2){echo ' input-staff-as';}
										echo '" id="staff'.$staff_count.'_as" name="staff'.$staff_count.'_as" placeholder="Zagrał jako..." value="';
										if(isset($related['play_as'])){echo $related['play_as'];}
										echo '">';
										echo '<button type="button" class="btn btn-danger btn-sm delete-staff">Usuń obsadę</button>';
									echo '</div>';
								}
							}
							?>
						</div>
						<button type="button" id="add-staff" class="btn btn-secondary">Dodaj obsadę</button>
						<div class="form-group form-hidden">
							<input type="text" class="form-control" id="id-user" value="<?php echo $_SESSION['id_user'];?>" name="id_user">
							<input type="text" class="form-control" id="hash" value="<?php echo $_SESSION['hash'];?>" name="hash">
							<input type="text" class="form-control" id="id-media" value="<?php echo $id;?>" name="id_media">
							<input type="text" class="form-control" id="relations-count" value="<?php echo $media_count; ?>" name="relations_count">
							<input type="text" class="form-control" id="staff-count" value="<?php echo $staff_count; ?>" name="staff_count">
						</div>
						<div class="form-group">
							<button type="submit" id="media-send" class="btn btn-primary btn-block" name="submit">Wyślij</button>
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
			$(document).ready(function() {
				var relations_count = <?php echo $media_count; ?>;
				
				$("#add-relation").click(function(e){
					e.preventDefault();
					relations_count++;
					$('#relations-count').val(relations_count);
					$("#relations-container").append('<div class="form-group"><input type="text" list="media_list" class="form-control form-control-top" id="relation'+relations_count+'" name="relation'+relations_count+'" placeholder="Tytuł powiązanej produkcji..."><select class="form-control form-control-bottom" id="relation'+relations_count+'_type" name="relation'+relations_count+'_type"><option value="0">Wybierz typ powiązania...</option><option value="1">Sequel</option><option value="2">Prequel</option><option value="3">Spin-off</option><option value="4">Inny</option></select><button type="button" class="btn btn-danger btn-sm delete-relation">Usuń relację</button></div>');
				});
				
				$("#relations-container").on("click",".delete-relation", function(e){
					e.preventDefault();
					$(this).parent('div').remove();
				});
				
				var staff_count = <?php echo $staff_count; ?>;
				
				$("#add-staff").click(function(e){
					e.preventDefault();
					staff_count++;
					$('#staff-count').val(staff_count);
					$("#staff-container").append('<div class="form-group"><input type="text" list="staff_list" class="form-control form-control-top" id="staff'+staff_count+'" name="staff'+staff_count+'"><select class="form-control form-control-bottom select-staff-type" id="staff'+staff_count+'_type" name="staff'+staff_count+'_type"><option value="0">Wybierz funkcję...</option><option value="1">Główny bohater</option><option value="2">Postać poboczna</option><option value="3">Reżyseria</option><option value="4">Scenariusz</option><option value="5">Zdjęcia</option><option value="6">Muzyka</option><option value="7">Produkcja</option><option value="8">Inny</option></select><input type="text" class="form-control form-control-bottom input-staff-as" id="staff'+staff_count+'_as" name="staff'+staff_count+'_as" placeholder="Zagrał jako..."><button type="button" class="btn btn-danger btn-sm delete-staff">Usuń obsadę</button></div>');
				});
				
				$("#staff-container").on("click",".delete-staff", function(e){
					e.preventDefault();
					$(this).parent('div').remove();
				});
				
				$("#staff-container").on("change", ".select-staff-type", function(e){
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
							$('#poster-previev').attr('src', e.target.result);
						}

						reader.readAsDataURL(input.files[0]);
					}
				}
			});
		</script>
	</body>
</html>