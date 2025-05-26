<?php 
include('config.php');
include('includes/all_functions.php');
include('includes/public/head_section.php'); 
?>

<title>MyWebSite | Article</title>
</head>
<body>
<div class="container">
    <?php include(ROOT_PATH . '/includes/public/navbar.php'); ?>
    <?php 
        if (isset($_GET['id'])) {
            $post = getPostById($_GET['id']);
            if (!$post || $post['published'] != 1) {
                echo "<div class='content'><h2>Article non trouvé ou non publié.</h2></div>";
            } else {
    ?>
    <div class="content">
        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
        <small>Publié le <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small><br>
        <?php if (!empty($post['image'])): ?>
            <img src="static/images/<?php echo htmlspecialchars($post['image']); ?>" alt="image" style="max-width: 400px;">
        <?php endif; ?>
        <div class="post-body" style="margin-top:20px;">
            <?php echo nl2br(htmlspecialchars($post['body'])); ?>
        </div>
    </div>
    <?php 
            }
        } else {
            echo "<div class='content'><h2>Aucun article spécifié.</h2></div>";
        }
    ?>
    <?php include(ROOT_PATH . '/includes/public/footer.php'); ?>
</div>
</body>
</html>
