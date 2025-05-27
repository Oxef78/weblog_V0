<?php
include('includes/public/registration_login.php');
include('includes/public/head_section.php');
if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}
?>

<title>WeblogResurrected | Sign in </title>
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
        <div style="width: 40%; margin: 20px auto;">
            <form method="post" action="login.php">
                <h2>Login</h2>
                <?php if (!empty($errors)): ?>
                    <div style="color:red;"><?php foreach ($errors as $e) echo $e."<br>"; ?></div>
                <?php endif; ?>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username">
                <input type="password" name="password" placeholder="Password">
                <button type="submit" class="btn" name="login_btn">Login</button>
                <p>
                    Not yet a member? <a href="register.php">Sign up</a>
                </p>
            </form>
        </div>
    </div>
    <?php include('includes/public/footer.php'); ?>
</body>
</html>
