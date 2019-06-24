<?php
require("../components/database.php");
session_start();

if(!isset($_SESSION['logged']) || !$_SESSION['logged'])
{
	$_SESSION['message'] = "Musisz się zalogować, aby przejść na tę stronę!";
	$_SESSION['message_type'] = "danger";
	header("Location: http://localhost/home/");
}

function shortenString($string, $max_chars)
{
	if(strlen($string) > $max_chars)
	{
		preg_match('/^.{0,' . $max_chars. '}(?:.*?)\b/iu', $string, $result);
		$result[0] .= '...';
		return $result[0];
	}
	else
	{
		if(empty($string))
			return 'Brak opisu!';
		else
			return $string;
	}
    
}

function returnType($type)
{
	switch($type)
	{
		case 1:
			return 'Film fabularny';
		case 2:
			return 'Film dokumentalny';
		case 3:
			return 'Film animowany';
		case 4:
			return 'Serial fabularny';
		case 5:
			return 'Serial dokumentalny';
		case 6:
			return 'Serial animowany';
		case 7:
			return 'Inny';
		default:
			return 'Brak danych';
	}
}

function returnImage($image, $type)
{
	if(empty($image))
	{
		switch($type)
		{
			case 1:
			case 2:
				return '../files/images/default.png';
			case 3:
				return '../files/images/avatar/u0-default.png';
		}
	}
	else
	{
		switch($type)
		{
			case 1:
				return '../files/images/cover-large/'.$image;
			case 2:
				return '../files/images/photo/'.$image;
			case 3:
				return '../files/images/avatar/'.$image;
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
						<?php
						if (!empty($_GET))
						{
							$phrase = $mysqli->escape_string($_GET['phrase']);
							$type = $mysqli->escape_string($_GET['type']);
							$format = $mysqli->escape_string($_GET['format']);
							$date_from = $mysqli->escape_string($_GET['date_from']);
							$date_to = $mysqli->escape_string($_GET['date_to']);
						}
						else
						{
							$phrase = '';
							$type = '1';
							$format = '';
							$date_from = '';
							$date_to = '';
						}
						?>
						<div class="col-sm-3">
							<form id="browse" action="../browse/" method="GET">
								<div class="form-group">
									<button class="btn btn-primary btn-block" type="submit" id="browse-submit">Szukaj</button>
								</div>
								<div class="form-group">
									<label for="phrase">Fraza</label>
									<input type="text" class="form-control" id="phrase" name="phrase" value="<?php echo $phrase ?>">
								</div>
								<div id="type-select" class="form-group">
									<label for="type">Typ</label>
									<select class="form-control" id="type" name="type">
										<option value="1" <?php if($type == '1'){echo 'selected';} ?>>Media</option>
										<option value="2" <?php if($type == '2'){echo 'selected';} ?>>Obsada</option>
										<option value="3" <?php if($type == '3'){echo 'selected';} ?>>Użytkownik</option>
									</select>
								</div>
								<div class="form-group media-only">
									<label for="format">Format</label>
									<select class="form-control" id="format" name="format">
										<option value="" <?php if($format == ''){echo 'selected';} ?>>Wszystkie</option>
										<option value="1" <?php if($format == '1'){echo 'selected';} ?>>Film fabularny</option>
										<option value="2" <?php if($format == '2'){echo 'selected';} ?>>Film dokumentalny</option>
										<option value="3" <?php if($format == '3'){echo 'selected';} ?>>Film animowany</option>
										<option value="4" <?php if($format == '4'){echo 'selected';} ?>>Serial fabularny</option>
										<option value="5" <?php if($format == '5'){echo 'selected';} ?>>Serial dokumentalny</option>
										<option value="6" <?php if($format == '6'){echo 'selected';} ?>>Serial animowany</option>
										<option value="7" <?php if($format == '7'){echo 'selected';} ?>>Inny</option>
									</select>
								</div>
								<?php
								$query = "SELECT MIN(premiere) as min, MAX(premiere) as max FROM media";
								$result = $mysqli->query($query) or die($mysqli->error());
								$premiere = $result->fetch_assoc();
								?>
								<div class="form-group media-only">
									<label for="date-from">Data premiery od</label>
									<input type="date" class="form-control" id="date-from" name="date_from" min="<?php echo $premiere['min'] ?>" max="<?php echo $premiere['max'] ?>" value="<?php if(empty($date_from)){ echo $premiere['min']; }else{echo $date_from;}?>" required>
								</div>
								<div class="form-group media-only">
									<label for="date-to">Data premiery do</label>
									<input type="date" class="form-control" id="date-to" name="date_to" min="<?php echo $premiere['min'] ?>" max="<?php echo $premiere['max'] ?>" value="<?php if(empty($date_to)){ echo $premiere['max']; }else{echo $date_to;}?>" required>
								</div>
								<div class="form-group media-only">
									<label for="genres">Gatunki</label>
									<div class="dropdown">
										<button class="btn btn-secondary dropdown-toggle genres-toggle" data-toggle="dropdown" type="button" id="dropdownMenuButton">Wybierz gatunki</button>
										<div class="dropdown-menu dropdown-menu-checkbox">
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-action" name="genre1" <?php if(isset($_GET['genre1'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-action">Akcja</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-adventure" name="genre2" <?php if(isset($_GET['genre2'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-adventure">Przygodowy</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-comedy" name="genre3" <?php if(isset($_GET['genre3'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-comedy">Komedia</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-drama" name="genre4" <?php if(isset($_GET['genre4'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-drama">Dramat</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-fantasy" name="genre5" <?php if(isset($_GET['genre5'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-fantasy">Fantastyka</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-horror" name="genre6" <?php if(isset($_GET['genre6'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-horror">Horror</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-mystery" name="genre7" <?php if(isset($_GET['genre7'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-mystery">Zagadka</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-psychological" name="genre8" <?php if(isset($_GET['genre8'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-psychological">Psychologiczny</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-romance" name="genre9" <?php if(isset($_GET['genre9'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-romance">Romans</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-scifi" name="genre10" <?php if(isset($_GET['genre10'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-scifi">Sci-Fi</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-sliceoflife" name="genre11" <?php if(isset($_GET['genre11'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-sliceoflife">Okruchy życia</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-sporting" name="genre12" <?php if(isset($_GET['genre12'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-sporting">Sportowy</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-supernatural" name="genre13" <?php if(isset($_GET['genre13'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-supernatural">Nadprzyrodzone</label>
											</div>
											<div class="form-check">
												<input type="checkbox" class="dropdown-checkbox" id="dropdown-thriller" name="genre14" <?php if(isset($_GET['genre14'])){echo 'checked';} ?>>
												<label class="dropdown-label" for="dropdown-thriller">Thriller</label>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div class="col-sm-9">
							<?php
							if (!empty($_GET))
							{
								switch($type)
								{
									case 1:
										$query = "SELECT * FROM media WHERE title LIKE '%$phrase%'";
										if(!empty($format)) { $query .= " AND type='$format'";}
										$query .= " AND premiere >= '$date_from' AND premiere <= '$date_to'";
										$first = true;
										for($x = 1; $x <= 14; $x++)
										{
											$genre = 'genre'.$x;
											if(isset($_GET[$genre]))
											{
												if($first == true)
												{
													$query .= " AND (genre LIKE '%|$x|%'";
													$first = false;
												}
												else
													$query .= " OR genre LIKE '%|$x|%'";
											}
										}
										if($first == false)
											$query .= ")";
										$result = $mysqli->query($query) or die($mysqli->error());
										
										if($result->num_rows == 0)
										{
											echo '<div class="alert alert-blue" role="alert">';
											echo 'Brak wyników wyszukiwania.';
											echo '</div>';
										}
										else
										{
											echo '<table class="table table-hover table-blue">';
												echo '<thead class="thead-blue">';
													echo '<tr>';
														echo '<th class="th-media-poster text-center" scope="col">Plakat</th>';
														echo '<th class="th-media-title text-center" scope="col">Tytuł</th>';
														echo '<th class="th-media-description text-center" scope="col">Opis</th>';
														echo '<th class="th-media-type text-center" scope="col">Format</th>';
													echo '</tr>';
												echo '</thead>';
												echo '<tbody>';
												while ($row = $result->fetch_assoc())
												{
													echo '<tr class="clickable-row" data-href="../media/'.$row['id'].'/">';
														echo '<td class="text-center text-middle"><img src="'.returnImage($row['poster'], 1).'" id="poster-image" class="poster-mini" ></td>';
														echo '<td class="text-center text-middle">'.$row['title'].'</td>';
														echo '<td class="text-center text-middle">'.shortenString($row['description'], 130).'</td>';
														echo '<td class="text-center text-middle">'.returnType($row['type']).'</td>';
													echo '</tr>';
												}
												echo '</tbody>';
											echo '</table>';
										}
										break;
									case 2:
										$query = "SELECT * FROM staff WHERE name LIKE '%".$_GET['phrase']."%'";
										$result = $mysqli->query($query) or die($mysqli->error());
										
										if($result->num_rows == 0)
										{
											echo '<div class="alert alert-blue" role="alert">';
											echo 'Brak wyników wyszukiwania.';
											echo '</div>';
										}
										else
										{
											echo '<table class="table table-hover table-blue">';
												echo '<thead class="thead-blue">';
													echo '<tr>';
														echo '<th class="th-staff-photo text-center" scope="col">Foto</th>';
														echo '<th class="th-staff-name text-center" scope="col">Nazwa</th>';
														echo '<th class="th-staff-description text-center" scope="col">Opis</th>';
													echo '</tr>';
												echo '</thead>';
												echo '<tbody>';
												while ($row = $result->fetch_assoc())
												{
													echo '<tr class="clickable-row" data-href="../staff/'.$row['id'].'/">';
														echo '<td class="text-center text-middle"><img src="'.returnImage($row['photo'], 2).'" id="photo-image" class="poster-mini" ></td>';
														echo '<td class="text-center text-middle">'.$row['name'].'</td>';
														echo '<td class="text-center text-middle">'.shortenString($row['description'], 150).'</td>';
													echo '</tr>';
												}
												echo '</tbody>';
											echo '</table>';
										}
										break;
									case 3:
										$query = "SELECT * FROM users WHERE username LIKE '%".$_GET['phrase']."%'";
										$result = $mysqli->query($query) or die($mysqli->error());
										
										if($result->num_rows == 0)
										{
											echo '<div class="alert alert-blue" role="alert">';
											echo 'Brak wyników wyszukiwania.';
											echo '</div>';
										}
										else
										{
											echo '<table class="table table-hover table-blue">';
												echo '<thead class="thead-blue">';
													echo '<tr>';
														echo '<th class="th-user-avatar text-center" scope="col">Awatar</th>';
														echo '<th class="th-user-username text-center" scope="col">Pseudonim</th>';
													echo '</tr>';
												echo '</thead>';
												echo '<tbody>';
												while ($row = $result->fetch_assoc())
												{
													echo '<tr class="clickable-row" data-href="../user/'.$row['id'].'/">';
														echo '<td class="text-center text-middle"><img src="'.returnImage($row['avatar'], 3).'" id="photo-image" class="poster-mini" ></td>';
														echo '<td class="text-center text-middle">'.$row['username'].'</td>';
													echo '</tr>';
												}
												echo '</tbody>';
											echo '</table>';
										}
										break;
									default:
										echo '<div class="alert alert-blue" role="alert">';
										echo 'Brak wyników wyszukiwania.';
										echo '</div>';
										break;
								}
							}
							else
							{
								echo '<div class="alert alert-blue" role="alert">';
								echo 'Brak wyników wyszukiwania.';
								echo '</div>';
							}
							?>
						</div>
					</div>
				</div>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script src="../js/user_actions.js"></script>
		<script>
			$('[href*="../browse/"]').addClass('active');
			
			var start_selected = $('#type').find('option:selected').attr('value');
			if(start_selected == '1')
			{
				$('.media-only').show();
			}
			else
			{
				$('.media-only').hide();
			}
			
			$("#type-select").on("change", "#type", function(e){
				var selected = $(this).find('option:selected').attr('value');
				if(selected == '1')
				{
					$('.media-only').show();
				}
				else
				{
					$('.media-only').hide();
				}
			});
			
			$(document).on('click', '.dropdown-menu', function (e) {
				e.stopPropagation();
			});
			
			$(".clickable-row").click(function() {
				window.location = $(this).data("href");
			});
		</script>
	</body>
</html>