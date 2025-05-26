<?php
include('config.php');

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$username = trim($_POST['username']);
	$password = $_POST['password'];
	$pwd = md5($password);

	if (empty($username) || empty($password)) {
		$errors[] = "Both fields required.";
	} else {
		// Recherche user en BDD
		$result = $conn->query("
				SELECT u.id, u.username, r.name AS role
				FROM users u
				LEFT JOIN role_user ru ON u.id = ru.user_id
				LEFT JOIN roles r ON r.id = ru.role_id
				WHERE u.username='$username' AND u.password='$pwd'
				LIMIT 1");
		if ($result && $result->num_rows == 1) {
			$user = $result->fetch_assoc();
			$_SESSION['user'] = [
				'id' => $user['id'],
				'username' => $user['username'],
				'role' => $user['role']
			];
			if ($user['role'] == 'Admin') {
				header('Location: admin/dashboard.php');
			} else {
				header('Location: index.php');
			}
			exit;
		} else {
			$errors[] = "Wrong username or password.";
		}
	}
}
?>

<?php include('includes/public/head_section.php'); ?>
<title>MyWebSite | Sign in </title>
</head>

<body>
	<div class="container">

		<!-- Navbar -->
		<?php include(ROOT_PATH . '/includes/public/navbar.php'); ?>
		<!-- // Navbar -->

		<div style="width: 40%; margin: 20px auto;">
			<form method="post" action="login.php">
				<h2>Login</h2>
				<?php
				// a revoir gestion de l'erreur
				if (!empty($errors)): ?>
					<div style="color:red;"><?php foreach ($errors as $e) echo $e."<br>"; ?></div>
				<?php endif; ?>
				<input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username">
				<input type="password" name="password" value="" placeholder="Password">
				<button type="submit" class="btn" name="login_btn">Login</button>
				<p>
					Not yet a member? <a href="register.php">Sign up</a>
				</p>
			</form>
		</div>
	</div>

	<!-- Footer -->
	<?php include(ROOT_PATH . '/includes/public/footer.php'); ?>
	<!-- // Footer -->
</body>
</html>
