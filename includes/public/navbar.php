<div class="navbar">
	<div class="logo_div">
		<h1>WeblogResurrected</h1>
	</div>
	<ul>
	  <li><a class="active" href="index.php">Home</a></li>
	  <li><a href="filtered_posts.php">Articles par thême</a></li>

      <!-- menu des posts pour les auteurs -->
	  <?php
	  if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Author') {
		  echo '<li><a href="admin/posts.php">Mes publications</a></li>';
	  }
	  ?>	

	  <li><a href="#news">News</a></li>
	  <li><a href="#contact">Contact</a></li>
	  <li><a href="#about">About</a></li>
	</ul>
</div>
