<?php include('config.php'); ?>
<?php include('includes/all_functions.php'); ?>
<?php include('includes/public/head_section.php'); ?>
<?php include('includes/public/registration_login.php');
$errors = [];
?>

<title>WeblogResurrected | Home </title>

<!-- <?php echo password_hash('1234', PASSWORD_DEFAULT); ?>
<?php var_dump($_SESSION); ?> dev, hors présentation -->
</head>

<body>

	<div class="container">

		<!-- Navbar -->
		 
		<?php
		echo session_save_path();
		
		if (session_status() == PHP_SESSION_NONE) session_start();

		if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin') {
			include(ROOT_PATH . '/includes/admin/navbar.php');
		} else {
			include(ROOT_PATH . '/includes/public/navbar.php');
		}
		?>
		<!-- // Navbar -->

		<!-- Banner -->
		<?php include(ROOT_PATH . '/includes/public/banner.php'); ?>
		<!-- // Banner -->

		<!-- Messages -->
		<?php include(ROOT_PATH . '/includes/public/messages.php'); ?>
		<!-- // Messages -->
		
		<!-- content -->
		<?php $posts = getPublishedPosts(); ?>


		<div class="content">
			<h2 class="content-title">Recent Articles</h2>
			<hr>
			<?php if (empty($posts)): ?>
				<p>Aucun article encore publié.</p>
			<?php else: ?>
				<?php foreach ($posts as $post): ?>
					<div class="post">
						<div class="post_image">
							<img src="static/images/<?php echo htmlspecialchars($post['image']); ?>" alt="">
						</div>
						<div class="post_info">
							<h3><?php echo htmlspecialchars($post['title']); ?></h3>
							<small>Publié le <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small>
							<p><?php echo nl2br(htmlspecialchars(substr($post['body'], 0, 150))); ?>...</p>
							<a href="single_post.php?id=<?php echo $post['id']; ?>" class="btn">Lire la suite</a>
						</div>
					</div>

				<?php endforeach; ?>
			<?php endif; ?>
		</div>



	<!-- Footer -->
	<?php include(ROOT_PATH . '/includes/public/footer.php'); ?>
	<!-- // Footer -->