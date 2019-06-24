<header class="masthead mb-auto cover-container">
				<div class="inner text-center">
					<h3 class="masthead-brand">Serialist</h3>
					<nav class="nav nav-masthead justify-content-center">
					<?php 
					if(isset($_SESSION['logged']))
					{
						if($_SESSION['logged'])
						{
							echo '<a class="nav-link" href="../browse/">Przeglądaj</a>';
							echo '<a class="nav-link" href="../user/'.$_SESSION['id_user'].'/">Profil</a>';
							echo '<a class="nav-link" href="../settings/">Ustawienia</a>';
							if($_SESSION['admin'] == 1)
							{
								echo '<a class="nav-link" href="../submissions/">Zgłoszenia</a>';
							}
							echo '<a class="nav-link" href="../logout/">Wyloguj</a>';
						}
					}
					else
					{
						echo '<a class="nav-link" href="../home/">Strona główna</a>';
						echo '<a class="nav-link" href="../login/">Zaloguj się</a>';
						echo '<a class="nav-link" href="../contact/">Kontakt</a>';
					}
					?>
					</nav>
				</div>
			</header>