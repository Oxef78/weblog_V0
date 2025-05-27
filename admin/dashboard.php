<?php
include('../config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
if (session_status() == PHP_SESSION_NONE) session_start();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Admin') {
    header('Location: ../login.php');
    exit;
}

// Compter le nombre total d'utilisateurs
$res_users = $conn->query("SELECT COUNT(*) as nb FROM users");
$total_users = $res_users ? $res_users->fetch_assoc()['nb'] : 0;

// Compter le nombre total de posts
$res_posts = $conn->query("SELECT COUNT(*) as nb FROM posts");
$total_posts = $res_posts ? $res_posts->fetch_assoc()['nb'] : 0;

// Compter le nombre de posts publiés
$res_published_posts = $conn->query("SELECT COUNT(*) as nb FROM posts WHERE published=1");
$total_topics = $conn->query("SELECT COUNT(*) as nb FROM topics")->fetch_assoc()['nb'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin | Dashboard</title>
    <?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
</head>
<body>

    <div class="header">
        <div class="logo">
			
            <a href="<?php echo BASE_URL . 'admin/dashboard.php' ?>">
                <h1>WeblogResurrected - Admin</h1>
            </a>
        </div>
        <?php if (isset($_SESSION['user'])) : ?>
		<a class="active" href="../index.php">Home</a>
        <a href="../filtered_posts.php">Rechercher un article</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="#">News</a>
        <a href="#">Contact</a>
        <a href="#">About</a>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
            <a href="<?php echo BASE_URL . '/logout.php'; ?>" class="logout-btn">logout</a>
        </div>
        <?php endif ?>
    </div>
    <div class="container dashboard">
        <h1>Welcome</h1>
        <div class="stats">
            <a href="users.php" class="first">
                <span><?php echo $total_users; ?></span> <br>
                <span>Newly registered users</span>
            </a>
            <a href="posts.php">
                <span><?php echo $total_posts; ?></span> <br>
                <span>All posts</span>
            </a>
            <a href="topics.php">
                <span><?php echo $total_topics; ?></span> <br>
                <span>All topics</span>
            </a>
        </div>
        <br><br><br>
        <div class="buttons">
            <a href="users.php">Add Users</a>
            <a href="posts.php">Add Posts</a>
        </div>
    </div>
</body>
</html>
