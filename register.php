<?php
session_start();
include('config.php');

$username = '';
$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['passwordConfirmation'];

    // Validation de base
    if (empty($username) || empty($email) || empty($password) || empty($passwordConfirmation)) {
        $errors[] = "All fields required.";
    }
    if ($password !== $passwordConfirmation) {
        $errors[] = "Passwords do not match.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is invalid.";
    }

    // Vérifier si déjà existant (username ou email)
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "User or email already exists.";
    }
    $stmt->close();

    // Après avoir check les erreurs
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT); //bcrypt actuellement

        //Quand PHP évolue (nouvel algo plus solide), les nouveaux mots de passe sont hashés avec ce nouvel algo.
        // anciens hash restent valides (PHP sait avec quel algo ils ont été faits, car le hash est préfixé, ex : $2y$ pour bcrypt).
        // le nom de l'algorithme est stocké dans le hash lui-même, donc si ça change ça foncitonne tjrs pr les anciens mdp

        // Insérer l'utilisateur
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param('sss', $username, $email, $password_hash);
        $stmt->execute();
        $user_id = $stmt->insert_id;
        $stmt->close();

        // Associer l'utilisateur au rôle 'Author'
        $role_res = $conn->query("SELECT id FROM roles WHERE name='Author' LIMIT 1");
        $role_id = $role_res->fetch_assoc()['id'];
        $conn->query("INSERT INTO role_user (user_id, role_id) VALUES ($user_id, $role_id)");

        $_SESSION['message'] = "Inscription réussie, bienvenue ! Vous pouvez maintenant vous connecter.";
        header('Location: login.php');
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
	if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin') {
		include('includes/admin/navbar.php');
	} else {
		include('includes/public/navbar.php');
	}
?>
<!-- Messages -->
<?php include('includes/public/messages.php'); ?>
<!-- // Messages -->
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
