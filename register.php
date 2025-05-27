<?php
include('config.php');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$username = '';
$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['passwordConfirmation'];

    if (empty($username) || empty($email) || empty($password) || empty($passwordConfirmation)) {
        $errors[] = "All fields required.";
    }
    if ($password !== $passwordConfirmation) {
        $errors[] = "Passwords do not match.";
    }

    // Vérifier si déjà existant (username ou email)
    $result = $conn->query("SELECT id FROM users WHERE username='$username' OR email='$email' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $errors[] = "User or email already exists.";
    }

    
    // Après avoir check les erreurs
if (empty($errors)) {
    $pwd = md5($password);

    // 1. Insérer l'utilisateur sans champ 'role'
    $conn->query("INSERT INTO users (username, email, password, created_at) VALUES ('$username', '$email', '$pwd', NOW())");
    $user_id = $conn->insert_id;

    // 2. Associer l'utilisateur au rôle 'Author'
    $res = $conn->query("SELECT id FROM roles WHERE name='Author' LIMIT 1");
    $role_id = $res->fetch_assoc()['id'];
    $conn->query("INSERT INTO role_user (user_id, role_id) VALUES ($user_id, $role_id)");

    // 3. Démarre session
    $_SESSION['user'] = ['id' => $user_id, 'username' => $username, 'role' => 'Author'];

    header('Location: index.php');
    exit;
}

}
?>

<?php include('includes/public/head_section.php'); ?>
<title>Register</title>
</head>
<body>
<div class="container">
<?php
	if (session_status() == PHP_SESSION_NONE) session_start();

	if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin') {
		include(ROOT_PATH . '/includes/admin/navbar.php');
	} else {
		include(ROOT_PATH . '/includes/public/navbar.php');
	}
?>

    <form method="post" action="register.php" style="width:40%;margin:30px auto;">
        <h2>Register</h2>
        <?php if (!empty($errors)): ?><div style="color:red;"><?php foreach($errors as $e) echo $e."<br>"; ?></div><?php endif; ?>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username">
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <input type="password" name="passwordConfirmation" placeholder="Confirm Password">
        <button type="submit" class="btn" name="register_btn">Register</button>
        <p>Already a member? <a href="login.php">Sign in</a></p>
    </form>
</div>
</body>
</html>
