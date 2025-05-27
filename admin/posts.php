<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/includes/admin_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/post_functions.php'); ?>
<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
<?php

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin', 'Author'])) {
    header('Location: ../login.php');
    exit;
}

$title = '';
$slug = '';
$body = '';
$image = '';
$topic_id = '';
$isEditingPost = false;
$post_id = 0;
$errors = [];

$topics = getAllTopics();


// Gestion publication/dépublication
if ($_SESSION['user']['role'] === 'Admin') {
    // Publier un post
    if (isset($_GET['publish'])) {
        $id = intval($_GET['publish']);
        $conn->query("UPDATE posts SET published=1 WHERE id=$id");
        header('Location: posts.php');
        exit;
    }
    // Dépublier un post (optionnel)
    if (isset($_GET['unpublish'])) {
        $id = intval($_GET['unpublish']);
        $conn->query("UPDATE posts SET published=0 WHERE id=$id");
        header('Location: posts.php');
        exit;
    }
}


// Suppression
if (isset($_GET['delete-post'])) {
    $post_id = intval($_GET['delete-post']);
    deletePost($post_id);
    header('Location: posts.php');
    exit;
}

// Edition (pré-remplissage)
if (isset($_GET['edit-post'])) {
    $isEditingPost = true;
    $post_id = intval($_GET['edit-post']);
    $posts = getAllPosts();
    foreach ($posts as $p) {
        if ($p['id'] == $post_id) {
            $title = $p['title'];
            $slug = $p['slug'];
            $body = $p['body'];
            $image = $p['image'];
            $topic_id = $p['topic_id'];
            break;
        }
    }
}

// Création/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $body = trim($_POST['body']);
    $topic_id = intval($_POST['topic_id']);
    // Upload image simplifié (à améliorer si besoin)
    $image = isset($_FILES['image']) && $_FILES['image']['name'] ? $_FILES['image']['name'] : '';
    if ($image != '') move_uploaded_file($_FILES['image']['tmp_name'], ROOT_PATH.'/static/images/'.$image);

    if (empty($title) || empty($slug) || empty($body) || empty($topic_id)) {
        $errors[] = "All fields required.";
    }

    if (empty($errors)) {
        if ($isEditingPost && isset($_POST['update_post'])) {
            updatePost($post_id, $title, $slug, $body, $image, $topic_id);
        } else {
            createPost($_SESSION['user']['id'], $title, $slug, $body, $image, $topic_id);
        }
        header('Location: posts.php');
        exit;
    }
}

// Liste des posts
if ($_SESSION['user']['role'] === 'Admin') {
    $posts = getAllPosts(); 
} else {
    $user_id = $_SESSION['user']['id'];
    $posts = getPostsByAuthor($user_id); 
}

?>

<title>Admin | Manage Posts</title>
</head>
<body>
<?php include(ROOT_PATH . '/includes/admin/header.php'); ?>
<div class="container content">
    <?php include(ROOT_PATH . '/includes/admin/menu.php'); ?>
    <div class="action">
        <h1 class="page-title"><?php echo $isEditingPost ? "Edit" : "Create"; ?> Post</h1>
        <form method="post" enctype="multipart/form-data" action="posts.php">
            <?php if (!empty($errors)): ?>
                <div style="color:red;"><?php foreach($errors as $e) echo $e."<br>"; ?></div>
            <?php endif; ?>
            <input type="text" name="title" placeholder="Title" value="<?php echo htmlspecialchars($title); ?>">
            <input type="text" name="slug" placeholder="Slug" value="<?php echo htmlspecialchars($slug); ?>">
            <textarea name="body" rows="6" placeholder="Body"><?php echo htmlspecialchars($body); ?></textarea>
            <input type="file" name="image">
            <select name="topic_id">
                <option value="" selected disabled>Choose topic</option>
                <?php foreach ($topics as $topic): ?>
                    <option value="<?php echo $topic['id']; ?>" <?php if($topic_id == $topic['id']) echo "selected"; ?>>
                        <?php echo $topic['name']; ?>
                    </option>
                <?php endforeach ?>
            </select>
            <?php if ($isEditingPost): ?>
                <button type="submit" class="btn" name="update_post">UPDATE</button>
            <?php else: ?>
                <button type="submit" class="btn" name="create_post">Save Post</button>
            <?php endif; ?>
        </form>
    </div>
    <div class="table-div">
    <h2>All Posts</h2>
        <table class="table">
            <thead>
                <th>#</th>
                <th>Title</th>
                <th>Author</th>
                <th>Topic</th>
                <th>Image</th>
                <th>Status</th>
                <?php if ($_SESSION['user']['role'] === 'Admin'): ?>
                    <th>Validation</th>
                <?php endif; ?>
                <th colspan="2">Actions</th>
            </thead>
            <tbody>
                <?php foreach ($posts as $k => $post): ?>
                <tr>
                    <td><?php echo $k+1; ?></td>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['username']); ?></td>
                    <td><?php echo isset($post['topic']) ? htmlspecialchars($post['topic']) : '<em>no topic</em>'; ?></td>
                    <td><?php echo htmlspecialchars($post['image']); ?></td>
                    <td>
                        <?php echo $post['published'] ? "<span style='color:green;'>Publié</span>" : "<span style='color:orange;'>En attente</span>"; ?>
                    </td>
                    <?php if ($_SESSION['user']['role'] === 'Admin'): ?>
                    <td>
                        <?php if (!$post['published']): ?>
                            <a href="posts.php?publish=<?php echo $post['id']; ?>" class="btn" onclick="return confirm('Publier cet article ?')">Publier</a>
                        <?php else: ?>
                            <a href="posts.php?unpublish=<?php echo $post['id']; ?>" class="btn" onclick="return confirm('Dépublier cet article ?')">Dépublier</a>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <td>
                        <a href="posts.php?edit-post=<?php echo $post['id']; ?>" class="fa fa-pencil btn edit"></a>
                    </td>
                    <td>
                        <a href="posts.php?delete-post=<?php echo $post['id']; ?>" class="fa fa-trash btn delete" onclick="return confirm('Delete this post?');"></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
