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
			<?php
				if ($_SERVER['REQUEST_METHOD'] == 'GET')
				{
					if(isset($_GET['id']) && !empty($_GET['id']))
					{
						$id = $_GET['id'];
						$result = $mysqli->query("SELECT * FROM media WHERE id='$id'") or die($mysqli->error());
						if ($result->num_rows == 0)
						{
							$_SESSION['message'] = "Brak zawartości o takim identyfikatorze!";
							$_SESSION['message_type'] = "warning";
							session_write_close();
							header("Location: http://localhost/browse/");
						}
						else
						{
							$media = $result->fetch_assoc();
						}
					}
					else
					{
						$_SESSION['message'] = "Brak zawartości o takim identyfikatorze!";
						$_SESSION['message_type'] = "warning";
						session_write_close();
						header("Location: http://localhost/browse/");
					}
				}
				else
				{
					$_SESSION['message'] = "Brak zawartości o takim identyfikatorze!";
					$_SESSION['message_type'] = "warning";
					session_write_close();
					header("Location: http://localhost/browse/");
				}
			?>

			<main role="main" class="inner cover">
				<div class="container">
					<?php require("../components/alerts.php") ?>
					<div class="row media-row-info">
						<div class="col-sm-auto text-center">
							<?php
							if(empty($media['poster']))
								echo '<img src="../files/images/default.png" class="poster" />';
							else
								echo '<img src="../files/images/cover-large/'.$media['poster'].'" class="poster" />';
							?>
						</div>
						<div class="col-sm">
							<p><big><b><?php echo $media['title'] ?></b></big>
							<?php
							$id_media = $_GET['id'];
							$query = "SELECT rating FROM users_list WHERE id_media='$id_media' AND rating != '0'";
							$result = $mysqli->query($query) or die($mysqli->error());
							if ($result->num_rows == 0)
							{
								echo '-/10 - 0 ocen';
							}
							else
							{
								$sum = 0;
								$count = 0;
								while ($row = $result->fetch_assoc())
								{
									$sum += $row['rating'];
									$count++;
								}
								$average = $sum / $count;
								echo number_format((float)$average, 2, ',', '').'/10 - '.$result->num_rows.' ocen/y';
							}
							?>
							</p>
							<p>
							<?php
							if(empty($media['description']))
								echo "Brak opisu.";
							else
								echo $media['description'];
							?>
							</p>
							<div class="container media-container-buttons">
								<div class="item media-button">
									<div class="dropdown">
										<?php
										$id_user = $_SESSION['id_user'];
										$hash = $_SESSION['hash'];
										$query = "SELECT * FROM users_list WHERE id_user='$id_user' AND id_media='$id_media'";
										$result = $mysqli->query($query) or die($mysqli->error());
										if ($result->num_rows == 0)
										{
											echo '<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dodaj do listy</button>';
											echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
												echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'5\')">Jako planowane</a>';
												echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'1\')">Jako oglądane</a>';
												echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'2\')">Jako ukończone</a>';
												echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'3\')">Jako wstrzymane</a>';
												echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'4\')">Jako porzucone</a>';
											echo '</div></div></div>';
										}
										else
										{
											$users_list = $result->fetch_assoc();
											echo '<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
											switch($users_list['status'])
											{
												case 1:
													echo 'Oglądane</button>';
													break;
												case 2:
													echo 'Ukończone</button>';
													break;
												case 3:
													echo 'Wstrzymane</button>';
													break;
												case 4:
													echo 'Porzucone</button>';
													break;
												case 5:
													echo 'Planowane</button>';
													break;
												default:
													break;
											}
											echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
												if($users_list['status'] != 5)
													echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'5\')">Planowane</a>';
												if($users_list['status'] != 1)
													echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'1\')">Oglądane</a>';
												if($users_list['status'] != 2)
													echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'2\')">Ukończone</a>';
												if($users_list['status'] != 3)
													echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'3\')">Wstrzymane</a>';
												if($users_list['status'] != 4)
													echo '<a class="dropdown-item" href="#" onclick="click_status(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'4\')">Porzucone</a>';
												echo '<div class="dropdown-divider"></div>';
												echo '<a class="dropdown-item" href="#" onclick="click_delete(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\')">Usuń z listy</a>';
											echo '</div></div></div>';

											$query = "SELECT episodes FROM media WHERE id='$id_media'";
											$result = $mysqli->query($query) or die($mysqli->error());
											$media_episodes = $result->fetch_assoc();
											echo '<div class="item media-button"><div class="dropdown">';
												echo '<button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
													if($users_list['progress'] == 0)
														echo 'Brak obejrzanych odcinków';
													else
														echo $users_list['progress'].'/'.$media_episodes['episodes'];
												echo '</button>';
												echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
													for($x = 0; $x <= $media_episodes['episodes']; $x++)
													{
														echo '<a class="dropdown-item" href="#" onclick="click_progress(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \''.$x.'\')">'.$x.'</a>';
													}
											echo '</div></div></div>';
											
											echo '<div class="item media-button"><div class="dropdown">';
												echo '<button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
													switch($users_list['rating'])
													{
														case 0:
															echo 'Brak oceny';
															break;
														case 1:
															echo '1/10';
															break;
														case 2:
															echo '2/10';
															break;
														case 3:
															echo '3/10';
															break;
														case 4:
															echo '4/10';
															break;
														case 5:
															echo '5/10';
															break;
														case 6:
															echo '6/10';
															break;
														case 7:
															echo '7/10';
															break;
														case 8:
															echo '8/10';
															break;
														case 9:
															echo '9/10';
															break;
														case 10:
															echo '10/10';
															break;
														
													}
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
											echo '</div></div></div>';
											
											echo '<div class="item media-button">';
											if($users_list['favourite'] == 1)
												echo '<a class="btn btn-danger" href="#" onclick="click_favourite(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'0\')"><i class="fas fa-heart"></i></a>';
											else
												echo '<a class="btn btn-danger" href="#" onclick="click_favourite(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\', \'1\')"><i class="far fa-heart"></i></a>';
											echo '</div>';
										}
										?>
									<div class="item media-button">
										<a href="../media_edit/<?php echo $id; ?>/" role="button" class="btn btn-warning">Edytuj</a>
									</div>
									<?php
									if($_SESSION['admin'] == 1)
									{
										echo '<div class="item media-button">';
											echo '<div class="dropdown">';
												echo '<button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</button>';
												echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
													echo '<a class="dropdown-item" href="#" onclick="click_delete_media(\''.$id_user.'\', \''.$id_media.'\', \''.$hash.'\')">Usuń media</a>';
												echo '</div>';
											echo '</div>';
										echo '</div>';
									}
									?>
							</div>
						</div>
					</div>
					<div class="row media-row-details">
						<div class="col-sm-2 media-col-attributes">
							<p><b>Typ:</b></br>
							<?php
							switch($media['type'])
							{
								case 1:
									echo 'Film fabularny';
									break;
								case 2:
									echo 'Film dokumentalny';
									break;
								case 3:
									echo 'Film animowany';
									break;
								case 4:
									echo 'Serial fabularny';
									break;
								case 5:
									echo 'Serial dokumentalny';
									break;
								case 6:
									echo 'Serial animowany';
									break;
								case 7:
									echo 'Inny';
									break;
								default:
									echo 'Brak danych';
									break;
							}							
							?></p>
							<p><b>Premiera:</b></br><?php echo $media['premiere'] ?></p>
							<?php
							if(isset($media['episodes']))
							{
								echo '<p><b>Liczba odcinków:</b></br>'.$media['episodes'].'</p>';
							}
							?>
							<p><b>Długość:</b></br><?php echo $media['duration'] ?> minut/y</p>
							<p><b>Gatunek:</b></br>
							<?php
							if($media['genre'] == '')
							{
								echo 'brak gatunku';
							}
							else
							{
								$genres = explode("|",$media['genre']);
								$x = 1;
								foreach($genres as $genre)
								{
									switch($genre)
									{
										case 1:
											echo 'Akcja';
											break;
										case 2:
											echo 'Przygodowy';
											break;
										case 3:
											echo 'Komedia';
											break;
										case 4:
											echo 'Dramat';
											break;
										case 5:
											echo 'Fantastyka';
											break;
										case 6:
											echo 'Horror';
											break;
										case 7:
											echo 'Zagadka';
											break;
										case 8:
											echo 'Psychologiczny';
											break;
										case 9:
											echo 'Romans';
											break;
										case 10:
											echo 'Sci-Fi';
											break;
										case 11:
											echo 'Okruchy życia';
											break;
										case 12:
											echo 'Sportowy';
											break;
										case 13:
											echo 'Nadprzyrodzone';
											break;
										case 14:
											echo 'Thriller';
											break;
										default:
											break;
									}
									if($x != 1 && $x < count($genres) - 1) {echo ', ';}
									$x++;
								}
							}
							?>
							</p>
						</div>
						<div class="col-sm">
							<div class="row media-row-connections-media">
								<div class="col-sm">
									<p><b>Powiązania</b></p>
									<?php
									$id = $media['id'];
									$result = $mysqli->query("SELECT * FROM related_media WHERE id_1 = $id ORDER BY type ASC") or die($mysqli->error());
									if ($result->num_rows == 0)
									{
										echo '<div class="alert alert-blue" role="alert">';
										echo "Brak powiązanych tytułów.";
										echo '</div>';
									}
									else
									{
										echo '<div class="container media-container-related">';
										while ($row = $result->fetch_assoc())
										{
											$media_related_id = $row['id_2'];
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
												switch($row['type'])
												{
													case 1:
														echo "Sequel";
														break;
													case 2:
														echo "Prequel";
														break;
													case 3:
														echo "Spin-off";
														break;
													case 4:
														echo "Inny";
														break;
													default:
														echo "Brak danych";
														break;
												}
												echo '</small></p></div></div></a>';
											}
										}
										echo '</div>';
									}
									?>
								</div>
							</div>
							<div class="row media-row-connections-staff">
								<div class="col-sm">
									<p><b>Obsada</b></p>
									<?php
									$id = $media['id'];
									$result = $mysqli->query("SELECT * FROM related_staff WHERE id_1 = $id ORDER BY type ASC") or die($mysqli->error());
									if ($result->num_rows == 0)
									{
										echo '<div class="alert alert-blue" role="alert">';
										echo "Brak powiązanej obsady.";
										echo '</div>';
									}
									else
									{
										echo '<div class="container media-container-staff">';
										while ($row = $result->fetch_assoc())
										{
											$staff_related_id = $row['id_2'];
											$result_related = $mysqli->query("SELECT * FROM staff WHERE id = $staff_related_id") or die($mysqli->error());
											if ($result_related->num_rows == 0)
											{
												echo "Brak wyników!";
											}
											else
											{
												$staff_related = $result_related->fetch_assoc();
												echo '<a class="media-item-link" href="../staff/'.$staff_related['id'].'/">';
												echo '<div class="item media-item-staff">';
												echo '<div class="item-image col-sm-auto float-left">';
												if(isset($staff_related['photo']))
													echo '<img src="../files/images/photo/'.$staff_related['photo'].'" class="poster-mini" />';
												else
													echo '<img src="../files/images/default.png" class="poster-mini" />';
												echo '</div><div class="col-sm-auto">';
												echo '<p class="media-item-description">'.$staff_related['name'].'</br>';
												if(!empty($row['play_as']))
												{
													echo '<small class="media-item-description-small">jako '.$row['play_as'].'</small></br>';
												}
												echo '<small>';
												switch($row['type'])
												{
													case 1:
														echo "Główny bohater";
														break;
													case 2:
														echo "Postać poboczna";
														break;
													case 3:
														echo "Reżyseria";
														break;
													case 4:
														echo "Scenariusz";
														break;
													case 5:
														echo "Zdjęcia";
														break;
													case 6:
														echo "Muzyka";
														break;
													case 7:
														echo "Produkcja";
														break;
													case 8:
														echo "Inny";
														break;
													default:
														echo "Brak danych";
														break;
												}
												echo '</small></p></div></div></a>';
											}
										}
										echo '</div>';
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>

			<?php require("../components/footer.php") ?>
		</div>
		<?php require("../components/scripts.php") ?>
		<script src="../js/user_actions.js"></script>
	</body>
</html>