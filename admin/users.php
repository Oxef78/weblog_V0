
<?php include('../config.php'); ?>

<?php include(ROOT_PATH . '/admin/admin_functions.php');  ?>
<?php include(ROOT_PATH . '/includes/all_functions.php'); ?>
<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
<?php 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Admin') {
    header('Location: ../login.php');
    exit;
}


$errors = [];
$username = "";
$email = "";
$role_id = "";
$isEditingUser = false;
$admin_id = 0;

// --- Gestion suppression ---
if (isset($_GET['delete-admin'])) {
    $admin_id = intval($_GET['delete-admin']);
    deleteAdmin($admin_id);
    $_SESSION['message'] = "L'utilisateur a bien été supprimé.";
    header('Location: users.php');
    exit;
}

// --- Gestion édition (préremplissage) ---
if (isset($_GET['edit-admin'])) {
    $isEditingUser = true;
    $admin_id = intval($_GET['edit-admin']);
    global $conn;
    $res = $conn->query("SELECT * FROM users WHERE id=$admin_id LIMIT 1");
    if ($res && $res->num_rows == 1) {
        $admin = $res->fetch_assoc();
        $username = $admin['username'];
        $email = $admin['email'];
        // Récupère le role_id via la table de jointure (role_user)
        $role_q = $conn->query("SELECT role_id FROM role_user WHERE user_id=$admin_id LIMIT 1");
        $role_id = ($role_q && $role_q->num_rows == 1) ? $role_q->fetch_assoc()['role_id'] : '';
    }
}

// --- Gestion création et modification ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['admin_id'])) {
        $admin_id = intval($_POST['admin_id']);
        $isEditingUser = true;
    }
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role_id = isset($_POST['role_id']) ? intval($_POST['role_id']) : '';
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['passwordConfirmation'];

    if (empty($username) || empty($email) || empty($password) || empty($passwordConfirmation) || empty($role_id)) {
        $errors[] = "All fields required.";
    }
    if ($password !== $passwordConfirmation) {
        $errors[] = "Passwords do not match.";
    }
    // Test unicité (username/email)
    global $conn;
    $check_sql = "SELECT id FROM users WHERE (username='$username' OR email='$email')";
    if ($isEditingUser) $check_sql .= " AND id!=$admin_id";
    $check = $conn->query($check_sql);
    if ($check && $check->num_rows > 0) {
        $errors[] = "Username or email already exists.";
    }

    if (empty($errors)) {
    if ($isEditingUser && isset($_POST['update_admin'])) {
        updateAdmin($admin_id, $username, $email, $role_id, $password);
        $_SESSION['message'] = "Utilisateur modifié avec succès.";
    } else {
        createAdmin($username, $email, $role_id, $password);
        $_SESSION['message'] = "Nouvel utilisateur créé avec succès.";
    }
    header('Location: users.php');
    exit;
}
}

// --- Récupération des rôles et admins ---
$roles = getAdminRoles();
$admins = getAdminUsers();
?>

<title>Admin | Manage users</title>
</head>

<body>
	<!-- admin navbar -->


	<?php include(ROOT_PATH . '/includes/admin/header.php') ?>
	<div class="container content">
		<!-- Left side menu -->
		<?php include(ROOT_PATH . '/includes/admin/menu.php') ?>

		<!-- Middle form - to create and edit  -->
		<div class="action">
			<h1 class="page-title">Create/Edit Admin User</h1>

			<form method="post" action="<?php echo BASE_URL . 'admin/users.php'; ?>">
			<?php if ($isEditingUser) : ?>
				<input type="hidden" name="admin_id" value="<?php echo $admin_id; ?>">
			<?php endif ?>
				<?php if (!empty($errors)): ?>
					<div style="color:red;"><?php foreach($errors as $e) echo $e."<br>"; ?></div>
				<?php endif; ?>

				<input type="text" name="username" value="<?php echo $username; ?>" placeholder="Username">
				<input type="email" name="email" value="<?php echo $email ?>" placeholder="Email">
				<input type="password" name="password" placeholder="Password">
				<input type="password" name="passwordConfirmation" placeholder="Password confirmation">

				<select name="role_id">
					<option value="" selected disabled>Assign role</option>
					<?php foreach ($roles as $role) : ?>
						<option value="<?php echo $role['id']; ?>" <?php if ($role_id == $role['id']) echo "selected"; ?>>
							<?php echo $role['role']; ?>
						</option>
					<?php endforeach ?>
				</select>
				<?php if ($isEditingUser) : ?>
					<button type="submit" class="btn" name="update_admin">UPDATE</button>
				<?php else : ?>
					<button type="submit" class="btn" name="create_admin">Save User</button>
				<?php endif ?>
			</form>
		</div>
		<!-- // Middle form - to create and edit -->

		<!-- Display records from DB-->
		<div class="table-div">
			<?php include(ROOT_PATH . '/includes/public/messages.php') ?>

			<?php if (empty($admins)) : ?>
				<h1>No admins in the database.</h1>
			<?php else : ?>
				<table class="table">
					<thead>
						<th>N</th>
						<th>Admin</th>
						<th>Role</th>
						<th colspan="2">Action</th>
					</thead>
					<tbody>
						<?php foreach ($admins as $key => $admin) : ?>
							<tr>
								<td><?php echo $key + 1; ?></td>
								<td>
									<?php echo $admin['username']; ?>, &nbsp;
									<?php echo $admin['email']; ?>
								</td>
								<td><?php echo $admin['role']; ?></td>
								<td>
									<a class="fa fa-pencil btn edit" href="users.php?edit-admin=<?php echo $admin['id'] ?>"></a>
								</td>
								<td>
									<a class="fa fa-trash btn delete" href="users.php?delete-admin=<?php echo $admin['id'] ?>" onclick="return confirm('Delete this user?');"></a>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			<?php endif ?>
		</div>
		<!-- // Display records from DB -->
	</div>
</body>
</html>
