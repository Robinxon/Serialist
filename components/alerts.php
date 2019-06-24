<?php
	if(!empty($_SESSION['message']))
	{
		switch($_SESSION['message_type'])
		{
			case "secondary":
				echo '<div class="alert alert-secondary" role="alert">'.$_SESSION['message'].'</div>';
				break;
			case "success":
				echo '<div class="alert alert-success" role="alert">'.$_SESSION['message'].'</div>';
				break;
			case "danger":
				echo '<div class="alert alert-danger" role="alert">'.$_SESSION['message'].'</div>';
				break;
			case "warning":
				echo '<div class="alert alert-warning" role="alert">'.$_SESSION['message'].'</div>';
				break;
			case "info":
				echo '<div class="alert alert-info" role="alert">'.$_SESSION['message'].'</div>';
				break;
			default:
				break;
		}
		
		$_SESSION['message'] = "";
		$_SESSION['message_type'] = "";
	}
?>