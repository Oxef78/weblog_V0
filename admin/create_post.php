<?php
include('../config.php');
include(ROOT_PATH . '/admin/admin_functions.php'); 
include(ROOT_PATH . '/admin/post_functions.php');
include(ROOT_PATH . '/includes/all_functions.php');
include(ROOT_PATH . '/includes/admin/head_section.php');

if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin', 'Author'])) {
    header('Location: ../login.php');
    exit;
}

// Initialisation variables formulaire
$title = "";
$slug = "";
$body = "";
$topic_id = "";
$errors = [];
$topics = getAllTopics();

// CRÉATION D’UN POST
if (isset($_POST['create_post'])) {
    $maxTitleLen = 255;
    $maxSlugLen = 255;
    $maxBodyLen = 10000;

    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']); 
    $body = trim($_POST['body']);
    $topic_id = intval($_POST['topic_id']);
    $user_id = $_SESSION['user']['id'];

    // Vérification de la longueur
    if (strlen($title) > $maxTitleLen) {
        $errors[] = "Title cannot exceed $maxTitleLen characters.";
    }
    if (strlen($slug) > $maxSlugLen) {
        $errors[] = "Slug cannot exceed $maxSlugLen characters.";
    }
    if (strlen($body) > $maxBodyLen) {
        $errors[] = "Body cannot exceed $maxBodyLen characters.";
    }

    // Gestion image
    $image = "";
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['name'] != "") {
        $image = basename($_FILES['featured_image']['name']);
        move_uploaded_file($_FILES['featured_image']['tmp_name'], ROOT_PATH . "/static/images/" . $image);
    }

    // Vérifier l’unicité du slug 
    $stmt = $conn->prepare("SELECT id FROM posts WHERE slug = ? LIMIT 1");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $check = $stmt->get_result();
    if ($check && $check->num_rows > 0) {
        $errors[] = "A post with this slug already exists. Please choose another slug.";
    }
    $stmt->close();

    // Vérification des champs obligatoires
    if (empty($title) || empty($slug) || empty($body) || empty($topic_id)) {
        $errors[] = "All fields are required.";
    }

    // Insertion en BDD si pas d’erreur
    if (empty($errors)) {
        $isAdmin = ($_SESSION['user']['role'] === 'Admin');
        $published = $isAdmin ? 1 : 0;

        // Insertion du post
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, slug, image, body, published, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param('issssi', $user_id, $title, $slug, $image, $body, $published);
        $stmt->execute();
        if ($stmt->affected_rows <= 0) {
            die('Erreur SQL : ' . $conn->error);
        }
        $new_post_id = $stmt->insert_id;
        $stmt->close();

        // Insertion du lien post-topic 
        $stmt = $conn->prepare("INSERT INTO post_topic (post_id, topic_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $new_post_id, $topic_id);
        $stmt->execute();
        $stmt->close();

        header('Location: posts.php');
        exit;
    }
}
?>


<title>Admin | Create Post</title>
</head>
<body>

    <!-- admin navbar -->
    <?php include(ROOT_PATH . '/includes/admin/header.php') ?>

    <div class="container content">
        <!-- Left side menu -->
        <?php include(ROOT_PATH . '/includes/admin/menu.php') ?>

        <!-- Middle form - to create ONLY -->
        <div class="action create-post-div">
            <h1 class="page-title">Create Post</h1>

            <form method="post" enctype="multipart/form-data" action="create_post.php">

                <!-- validation errors for the form -->
                <?php if (!empty($errors)): ?>
                    <div style="color:red;"><?php foreach($errors as $e) echo $e."<br>"; ?></div>
                <?php endif; ?>

                <input 
                    type="text"
                    name="title"
                    value="<?php echo htmlspecialchars($title); ?>" 
                    placeholder="Title"
                >
                <input
                    type="text"
                    name="slug"
                    value="<?php echo htmlspecialchars($slug); ?>"
                    placeholder="Slug (no spaces, only letters, numbers, dashes)"
                >

                <label style="float: left; margin: 5px auto 5px;">Featured image</label>
                <input 
                    type="file"
                    name="featured_image"
                >

                <textarea name="body" id="body" cols="30" rows="10"><?php echo htmlspecialchars($body); ?></textarea>
                
                <select name="topic_id">
                    <option value="" selected disabled>Choose topic</option>
                    <?php foreach ($topics as $topic): ?>
                        <option value="<?php echo $topic['id']; ?>" <?php if ($topic_id == $topic['id']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($topic['name']); ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <button type="submit" class="btn" name="create_post">Save Post</button>
            </form>
        </div>
        <!-- // Middle form - to create only -->
    </div>
</body>
</html>

<script>
    CKEDITOR.replace('body');
</script>
