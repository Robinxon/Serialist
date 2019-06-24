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
						$result = $mysqli->query("SELECT * FROM staff WHERE id='$id'") or die($mysqli->error());
						if ($result->num_rows == 0)
						{
							$_SESSION['message'] = "Brak zawartości o takim identyfikatorze!";
							$_SESSION['message_type'] = "warning";
							session_write_close();
							header("Location: http://localhost/browse/");
						}
						else
						{
							$staff = $result->fetch_assoc();
							$id_user = $_SESSION['id_user'];
							$hash = $_SESSION['hash'];
							$id_staff = $_GET['id'];
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
					<div class="row staff-row-info">
						<div class="col-sm-auto text-center">
							<?php
								if(empty($staff['photo']))
									echo '<img src="../files/images/default.png" class="poster" />';
								else
									echo '<img src="../files/images/photo/'.$staff['photo'].'" class="poster" />';
							?>
						</div>
						<div class="col-sm">
							<p><big><b><?php echo $staff['name'] ?></b></big></p>
							<p><?php
							if(empty($staff['description']))
								echo "Brak opisu.";
							else
								echo $staff['description'];
							?></p>
							<div class="container media-container-buttons">
								<div class="item media-button">
									<a href="../staff_edit/<?php echo $id; ?>/" role="button" class="btn btn-warning">Edytuj</a>
								</div>
								<?php
								if($_SESSION['admin'] == 1)
								{
									echo '<div class="item media-button">';
										echo '<div class="dropdown">';
											echo '<button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</button>';
											echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
												echo '<a class="dropdown-item" href="#" onclick="click_delete_staff(\''.$id_user.'\', \''.$id_staff.'\', \''.$hash.'\')">Usuń obsadę</a>';
											echo '</div>';
										echo '</div>';
									echo '</div>';
								}
								?>
							</div>
						</div>
					</div>
					<div class="row staff-row-details">
						<div class="col-sm">
							<div class="row staff-row-connections">
								<div class="col-sm">
									<p><b>Powiązania</b></p>
									<?php
									$id = $staff['id'];
									$result = $mysqli->query("SELECT related_staff.id_1, related_staff.type, related_staff.play_as FROM related_staff LEFT JOIN media ON related_staff.id_1 = media.id WHERE id_2 = $id ORDER BY media.title") or die($mysqli->error());
									if ($result->num_rows == 0)
									{
										echo "Brak powiązanych tytułów.";
									}
									else
									{
										echo '<div class="container media-container-related">';
										while ($row = $result->fetch_assoc())
										{
											$media_related_id = $row['id_1'];
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
													default:
														echo "Brak danych";
														break;
												}
												echo '</small></br>';
												if(!empty($row['play_as']))
												{
													echo '<small class="media-item-description-small">jako '.$row['play_as'].'</small></br>';
												}
												echo '</p></div></div></a>';
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