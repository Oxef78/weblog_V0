<?php include('config.php'); ?>
<?php include('includes/all_functions.php'); ?>
<?php include('includes/public/head_section.php'); ?>
<?php
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_btn'])) {
    // le code de vérif comme vu plus haut...
}
?>

<title>MyWebSite | Home </title>


</head>

<body>

	<div class="container">

		<!-- Navbar -->
		<?php include(ROOT_PATH . '/includes/public/navbar.php'); ?>
		<!-- // Navbar -->

		<!-- Banner -->
		<?php include(ROOT_PATH . '/includes/public/banner.php'); ?>
		<!-- // Banner -->

		<!-- Messages -->
		
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
					<div class="post" style="margin-bottom: 30px;">
						<h3><?php echo htmlspecialchars($post['title']); ?></h3>
						<small>Publié le <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small>
						<p><?php echo nl2br(htmlspecialchars(substr($post['body'], 0, 150))); ?>...</p>
						<a href="single_post.php?id=<?php echo $post['id']; ?>" class="btn">Lire la suite</a>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>



	<!-- Footer -->
	<?php include(ROOT_PATH . '/includes/public/footer.php'); ?>
	<!-- // Footer -->