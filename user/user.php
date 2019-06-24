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
		<base href='http://localhost/'>

		<!-- CSS -->
		<link href="../css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/panel.css" rel="stylesheet">

		<title>Serialist</title>
	</head>
	<body>
		<div id="wrapper" class="d-flex w-100 h-100 p-3 mx-auto flex-column">
			<?php require("../components/header.php") ?>
			<?php
				if ($_SERVER['REQUEST_METHOD'] == 'GET')
				{
					if(isset($_GET['id']) && !empty($_GET['id']))
					{
						$id = $_GET['id'];
						$result = $mysqli->query("SELECT * FROM users WHERE id='$id'") or die($mysqli->error());
						if ($result->num_rows == 0)
						{
							$_SESSION['message'] = "Brak użytkownika o takim identyfikatorze!";
							$_SESSION['message_type'] = "warning";
							session_write_close();
							header("Location: http://localhost/browse/");
						}
						else
						{
							$user = $result->fetch_assoc();
						}
					}
					else
					{
						$_SESSION['message'] = "Brak użytkownika o takim identyfikatorze!";
						$_SESSION['message_type'] = "warning";
						session_write_close();
						header("Location: http://localhost/browse/");
					}
				}
				else
				{
					$_SESSION['message'] = "Brak użytkownika o takim identyfikatorze!";
					$_SESSION['message_type'] = "warning";
					session_write_close();
					header("Location: http://localhost/browse/");
				}
			?>

			<main role="main" class="inner cover">
				<div class="container">
					<?php require("../components/alerts.php") ?>
					<div class="row user-row-info">
						<div class="col-sm-auto text-center">
							<?php
								if(isset($user['avatar']))
									echo '<img src="../files/images/avatar/'.$user['avatar'].'" class="avatar" />';
								else
									echo '<img src="../files/images/avatar/u0-default.png" class="avatar" />';
							?>
						</div>
						<div class="col-sm">
							<p><big><b><?php echo $user['username'] ?></b></big></p>
							<?php
							$id_user = $_GET['id'];
							$query = "SELECT AVG(rating) as average FROM users_list WHERE id_user = '$id_user' AND rating != '0'";
							$result = $mysqli->query($query) or die($mysqli->error());
							$average_rating = $result->fetch_assoc();
							echo '<span>Średnia wystawionych ocen: <b>'.number_format((float)$average_rating['average'], 2, ',', '').'</b></span></br>';
							
							$query = "SELECT SUM(ul.progress * m.duration) as sum FROM users_list ul LEFT JOIN media m ON ul.id_media = m.id WHERE id_user = '$id_user'";
							$result = $mysqli->query($query) or die($mysqli->error());
							$sum_progress = $result->fetch_assoc();
							if(empty($sum_progress['sum']))
								$watched = 0;
							else
								$watched = $sum_progress['sum'];
							echo '<span>Obejrzanych produkcji w minutach: <b>'.$watched.'</b></span></br>';
							
							$query = "SELECT COUNT(id) as sum, status FROM users_list WHERE id_user='$id_user' GROUP BY status";
							$result = $mysqli->query($query) or die($mysqli->error());
							$x = 1;
							while ($row = $result->fetch_assoc())
							{
								switch($x)
								{
									case 1:
										if($row['status'] == $x)
										{
											$status_watching = $row['sum'];
											break;
										}
										else
										{
											$status_watching = 0;
											$x++;
										}
									case 2:
										if($row['status'] == $x)
										{
											$status_completed = $row['sum'];
											break;
										}
										else
										{
											$status_completed = 0;
											$x++;
										}
									case 3:
										if($row['status'] == $x)
										{
											$status_paused = $row['sum'];
											break;
										}
										else
										{
											$status_paused = 0;
											$x++;
										}
									case 4:
										if($row['status'] == $x)
										{
											$status_dropped = $row['sum'];
											break;
										}
										else
										{
											$status_dropped = 0;
											$x++;
										}
									case 5:
										if($row['status'] == $x)
										{
											$status_planned = $row['sum'];
											break;
										}
										else
										{
											$status_planned = 0;
											$x++;
										}
								}
								$x++;
							}
							while($x < 6)
							{
								switch($x)
								{
									case 1:
										$status_watching = 0;
										$x++;
										break;
									case 2:
										$status_completed = 0;
										$x++;
										break;
									case 3:
										$status_paused = 0;
										$x++;
										break;
									case 4:
										$status_dropped = 0;
										$x++;
										break;
									case 5:
										$status_planned = 0;
										$x++;
										break;
								}
							}
							
							$status_sum = $status_watching + $status_completed + $status_paused + $status_dropped + $status_planned;
							
							if($status_sum != 0)
							{
								$status_watching_percentage = ($status_watching / $status_sum) * 100;
								$status_completed_percentage = ($status_completed / $status_sum) * 100;
								$status_paused_percentage = ($status_paused / $status_sum) * 100;
								$status_dropped_percentage = ($status_dropped / $status_sum) * 100;
								$status_planned_percentage = ($status_planned / $status_sum) * 100;
								
								echo '<div class="progress user-progress">';
									echo '<div class="progress-bar" role="progressbar" style="width: '.$status_watching_percentage.'%" aria-valuenow="'.$status_watching_percentage.'" aria-valuemin="0" aria-valuemax="100">Oglądane</div>';
									echo '<div class="progress-bar bg-success" role="progressbar" style="width: '.$status_completed_percentage.'%" aria-valuenow="'.$status_completed_percentage.'" aria-valuemin="0" aria-valuemax="100">Ukończone</div>';
									echo '<div class="progress-bar bg-warning" role="progressbar" style="width: '.$status_paused_percentage.'%" aria-valuenow="'.$status_paused_percentage.'" aria-valuemin="0" aria-valuemax="100">Wstrzymane</div>';
									echo '<div class="progress-bar bg-danger" role="progressbar" style="width: '.$status_dropped_percentage.'%" aria-valuenow="'.$status_dropped_percentage.'" aria-valuemin="0" aria-valuemax="100">Porzucone</div>';
									echo '<div class="progress-bar bg-info" role="progressbar" style="width: '.$status_planned_percentage.'%" aria-valuenow="'.$status_planned_percentage.'" aria-valuemin="0" aria-valuemax="100">Planowane</div>';
								echo '</div>';
							}
							?>
						</div>
					</div>
					<div class="row user-row-favourites">
						<div class="col-sm">
							<p><b>Ulubione</b></p>
							<?php
							$id_user = $_GET['id'];
							$result = $mysqli->query("SELECT * FROM users_list WHERE id_user = '$id_user' AND favourite=1") or die($mysqli->error());
							if ($result->num_rows == 0)
							{
								echo '<div class="alert alert-blue" role="alert">';
								echo "Brak ulubionych tytułów.";
								echo '</div>';
							}
							else
							{
								echo '<div class="container media-container-related">';
								while ($row = $result->fetch_assoc())
								{
									$media_related_id = $row['id_media'];
									$result_related = $mysqli->query("SELECT * FROM media WHERE id = $media_related_id") or die($mysqli->error());
									if ($result_related->num_rows == 0)
									{
										echo "Brak wyników!";
									}
									else
									{
										$media_related = $result_related->fetch_assoc();
										echo '<a class="media-item-link" href="../media/'.$media_related['id'].'/">';
										echo '<div class="item media-item-related">';
										echo '<div class="item-image col-sm-auto float-left">';
										if(isset($media_related['poster']))
											echo '<img src="../files/images/cover-large/'.$media_related['poster'].'" class="poster-mini" />';
										else
											echo '<img src="../files/images/default.png" class="poster-mini" />';
										echo '</div><div class="col-sm-auto">';
										echo '<p class="media-item-description">'.$media_related['title']."</br><small>";
										$query = "SELECT rating FROM users_list WHERE id_media='".$media_related['id']."' AND rating != '0'";
										$result_rating = $mysqli->query($query) or die($mysqli->error());
										if ($result_rating->num_rows == 0)
										{
											echo '-/10';
										}
										else
										{
											$sum = 0;
											$count = 0;
											while ($row = $result_rating->fetch_assoc())
											{
												$sum += $row['rating'];
												$count++;
											}
											$average = $sum / $count;
											echo number_format((float)$average, 2, ',', '').'/10';
										}
										echo '</small></p></div></div></a>';
									}
								}
								echo '</div>';
							}
							?>
						</div>
					</div>
					<?php
					$hash = $_SESSION['hash'];
					for ($x = 1; $x <= 5; $x++) {
						switch($x)
						{
							case 1:
								echo '<div class="row user-row-watching">';
								echo '<div class="col-sm">';
								echo '<p><b>Oglądane</b></p>';
								break;
							case 2:
								echo '<div class="row user-row-completed">';
								echo '<div class="col-sm">';
								echo '<p><b>Ukończone</b></p>';
								break;
							case 3:
								echo '<div class="row user-row-paused">';
								echo '<div class="col-sm">';
								echo '<p><b>Wstrzymane</b></p>';
								break;
							case 4:
								echo '<div class="row user-row-dropped">';
								echo '<div class="col-sm">';
								echo '<p><b>Porzucone</b></p>';
								break;
							case 5:
								echo '<div class="row user-row-planned">';
								echo '<div class="col-sm">';
								echo '<p><b>Planowane</b></p>';
								break;
						}
						
						$query = "SELECT media.id, media.title, media.poster, media.episodes, users_list.progress, users_list.rating FROM users_list LEFT JOIN media ON users_list.id_media = media.id WHERE users_list.id_user = '$id_user' AND users_list.status='$x'";
						$result = $mysqli->query($query) or die($mysqli->error());
						
						if ($result->num_rows == 0)
						{
							switch($x)
							{
								case 1:
									echo '<div class="alert alert-blue" role="alert">';
									echo "Brak oglądanych tytułów.";
									echo '</div>';
									break;
								case 2:
									echo '<div class="alert alert-blue" role="alert">';
									echo "Brak ukończonych tytułów.";
									echo '</div>';
									break;
								case 3:
									echo '<div class="alert alert-blue" role="alert">';
									echo "Brak wstrzymanych tytułów.";
									echo '</div>';
									break;
								case 4:
									echo '<div class="alert alert-blue" role="alert">';
									echo "Brak porzuconych tytułów.";
									echo '</div>';
									break;
								case 5:
									echo '<div class="alert alert-blue" role="alert">';
									echo "Brak planowanych tytułów.";
									echo '</div>';
									break;
							}
						}
						else
						{
							echo '<table class="table table-sm table-hover table-blue">';
							echo '<thead class="thead-blue">';
								echo '<tr>';
									echo '<th class="th-count" scope="col">#</th>';
									echo '<th class="th-title" scope="col">Tytuł</th>';
									echo '<th class="th-rating text-center" scope="col">Ocena</th>';
									echo '<th class="th-progress text-center" scope="col">Postęp</th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							$counter = 1;
							while ($row = $result->fetch_assoc())
							{
								$id_media = $row['id'];
								echo '<tr class="tooltip-holder" data-toggle="tooltip" data-placement="left" title="<img src=\'../files/images/cover-large/'.$row['poster'].'\' class=\'poster-mini\' />">';
									echo '<td>';
										echo $counter;
									echo '</td>';
									echo '<td>';
										echo '<a href="../media/'.$row['id'].'/">'.$row['title'].'</a>';
									echo '</td>';
									echo '<td class="text-center text-middle">';
											if($_SESSION['id_user'] == $_GET['id'])
											{
												echo '<div class="dropdown">';
													echo '<button class="btn-sm btn-secondary dropdown-toggle dropdown-table" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
											}
											switch($row['rating'])
											{
												case 0:
													echo '-';
													break;
												case 1:
													echo '1';
													break;
												case 2:
													echo '2';
													break;
												case 3:
													echo '3';
													break;
												case 4:
													echo '4';
													break;
												case 5:
													echo '5';
													break;
												case 6:
													echo '6';
													break;
												case 7:
													echo '7';
													break;
												case 8:
													echo '8';
													break;
												case 9:
													echo '9';
													break;
												case 10:
													echo '10';
													break;
												
											}
											if($_SESSION['id_user'] == $_GET['id'])
											{
												echo '</button>';
												echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'0\')">Brak oceny</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'1\')">1</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'2\')">2</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'3\')">3</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'4\')">4</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'5\')">5</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'6\')">6</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'7\')">7</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'8\')">8</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'9\')">9</a>';
													echo '<a class="dropdown-item" href="#" onclick="click_rate(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'10\')">10</a>';
												echo '</div></div>';
											}
									echo '</td>';
									echo '<td class="text-center text-middle">';
										if($row['progress'] > 0)
											echo $row['progress'];
										else
											echo '-';
										echo '/'.$row['episodes'];
										if($_SESSION['id_user'] == $_GET['id'] && $row['progress'] < $row['episodes'])
											echo ' <a href="#" onclick="click_progress_plus(\''.$id_user.'\', \''.$row['id'].'\', \''.$hash.'\')">+</a>';
									echo '</td>';
								echo '</tr>';
								$counter++;
							}
							echo '</tbody>';
							echo '</table>';
							echo '</div></div>';
						}
					}
					?>
				</div>
			</main>
			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script src="../js/user_actions.js"></script>
		<script>
			$('[href*="../user/<?php if($_GET['id'] == $_SESSION['id_user']) {echo $_SESSION['id_user'];} ?>/"]').addClass('active');
			$(function () {
				$('[data-toggle="tooltip"]').tooltip({
					animated: 'fade',
					placement: 'left',
					html: true
				});
			});
		</script>
	</body>
</html>