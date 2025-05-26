<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/includes/admin_functions.php'); ?>
<?php include(ROOT_PATH . '/includes/admin/head_section.php'); ?>
<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Admin') {
    header('Location: ../login.php');
    exit;
}

$name = "";
$slug = "";
$topic_id = 0;
$isEditingTopic = false;
$errors = [];

// Gestion suppression
if (isset($_GET['delete-topic'])) {
    $topic_id = intval($_GET['delete-topic']);
    deleteTopic($topic_id);
    header('Location: topics.php');
    exit;
}

// Gestion édition
if (isset($_GET['edit-topic'])) {
    $isEditingTopic = true;
    $topic_id = intval($_GET['edit-topic']);
    $topics = getAllTopics();
    foreach ($topics as $t) {
        if ($t['id'] == $topic_id) {
            $name = $t['name'];
            $slug = $t['slug'];
            break;
        }
    }
}

// Création/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);

    if (empty($name) || empty($slug)) {
        $errors[] = "All fields required.";
    }
    if (empty($errors)) {
        if ($isEditingTopic && isset($_POST['update_topic'])) {
            updateTopic($topic_id, $name, $slug);
        } else {
            createTopic($name, $slug);
        }
        header('Location: topics.php');
        exit;
    }
}

// Liste des topics
$topics = getAllTopics();
?>

<title>Admin | Topics</title>
</head>
<body>
<?php include(ROOT_PATH . '/includes/admin/header.php'); ?>
<div class="container content">
    <?php include(ROOT_PATH . '/includes/admin/menu.php'); ?>
    <div class="action">
        <h1 class="page-title"><?php echo $isEditingTopic ? 'Edit' : 'Create'; ?> Topic</h1>
        <form method="post" action="topics.php">
            <?php if (!empty($errors)): ?><div style="color:red;"><?php foreach($errors as $e) echo $e."<br>"; ?></div><?php endif; ?>
            <input type="text" name="name" placeholder="Topic name" value="<?php echo htmlspecialchars($name); ?>">
            <input type="text" name="slug" placeholder="Slug" value="<?php echo htmlspecialchars($slug); ?>">
            <?php if ($isEditingTopic): ?>
                <button type="submit" class="btn" name="update_topic">UPDATE</button>
            <?php else: ?>
                <button type="submit" class="btn" name="create_topic">Save Topic</button>
            <?php endif; ?>
        </form>
    </div>
    <div class="table-div">
        <h2>All Topics</h2>
        <table class="table">
            <thead>
                <th>#</th><th>Name</th><th>Slug</th><th colspan="2">Actions</th>
            </thead>
            <tbody>
                <?php foreach ($topics as $k => $topic): ?>
                <tr>
                    <td><?php echo $k+1; ?></td>
                    <td><?php echo htmlspecialchars($topic['name']); ?></td>
                    <td><?php echo htmlspecialchars($topic['slug']); ?></td>
                    <td><a href="topics.php?edit-topic=<?php echo $topic['id']; ?>" class="fa fa-pencil btn edit"></a></td>
                    <td><a href="topics.php?delete-topic=<?php echo $topic['id']; ?>" class="fa fa-trash btn delete" onclick="return confirm('Delete this topic?');"></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
