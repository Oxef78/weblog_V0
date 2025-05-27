<?php
include('../config.php');
include(ROOT_PATH . '/includes/admin_functions.php');
include(ROOT_PATH . '/admin/post_functions.php');
include(ROOT_PATH . '/includes/all_functions.php');
include(ROOT_PATH . '/includes/admin/head_section.php');

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin', 'Author'])) {
    header('Location: ../login.php');
    exit;
}



// Initialisation variables formulaire
$title = "";
$body = "";
$topic_id = "";
$isEditingPost = false;
$post_id = 0;
$errors = [];

$topics = getAllTopics();	

// EDITION D’UN POST
if (isset($_GET['edit'])) {
    $isEditingPost = true;
    $post_id = intval($_GET['edit']);
    $sql = "SELECT * FROM posts WHERE id = $post_id LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 1) {
        $post = $result->fetch_assoc();
        $title = $post['title'];
        $body = $post['body'];
        $featured_image = $post['image'];
        // Récupérer le topic_id via la table post_topic
        $topic_id = "";
        $topic_res = $conn->query("SELECT topic_id FROM post_topic WHERE post_id = $post_id LIMIT 1");
        if ($topic_res && $topic_res->num_rows == 1) {
            $topic_row = $topic_res->fetch_assoc();
            $topic_id = $topic_row['topic_id'];
        }
    }
}

// CREATION D’UN POST
if (isset($_POST['create_post'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $body = mysqli_real_escape_string($conn, $_POST['body']);
    $topic_id = intval($_POST['topic_id']);

    // Gestion image
    $image = "";
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['name'] != "") {
        $image = basename($_FILES['featured_image']['name']);
        move_uploaded_file($_FILES['featured_image']['tmp_name'], ROOT_PATH . "/static/images/" . $image);
    }

    // Générer un slug simple (pour démo)
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));
    // L’utilisateur connecté : pour l’instant on prend admin 1 (à améliorer)
    $user_id = 1;

    // Check si le slug existe déjà
    $check = $conn->query("SELECT id FROM posts WHERE slug = '$slug' LIMIT 1");
    if ($check && $check->num_rows > 0) {
        $errors[] = "A post with this slug already exists. Please change the title.";
    }

    if (empty($errors)) {
        $isAdmin = (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'Admin');
        $published = $isAdmin ? 1 : 0;
        $sql = "INSERT INTO posts (user_id, title, slug, image, body, published, created_at) VALUES ($user_id, '$title', '$slug', '$image', '$body', 1, NOW())";
        $conn->query($sql);
        $new_post_id = $conn->insert_id;
        // Insérer dans post_topic
        $conn->query("INSERT INTO post_topic (post_id, topic_id) VALUES ($new_post_id, $topic_id)");

        header('Location: posts.php');
        exit;
    }
}

// MISE À JOUR D’UN POST
if (isset($_POST['update_post'])) {
    $post_id = intval($_POST['post_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $body = mysqli_real_escape_string($conn, $_POST['body']);
    $topic_id = intval($_POST['topic_id']);

    // Gestion image (si upload d'une nouvelle image)
    $image_sql = "";
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['name'] != "") {
        $image = basename($_FILES['featured_image']['name']);
        move_uploaded_file($_FILES['featured_image']['tmp_name'], ROOT_PATH . "/static/images/" . $image);
        $image_sql = ", image='$image'";
    }

    // Générer le slug à nouveau si modifié
    $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));
    // Check si le slug existe déjà pour un autre post
    $check = $conn->query("SELECT id FROM posts WHERE slug = '$slug' AND id != $post_id LIMIT 1");
    if ($check && $check->num_rows > 0) {
        $errors[] = "A post with this slug already exists. Please change the title.";
    }

    if (empty($errors)) {
        $sql = "UPDATE posts SET title='$title', slug='$slug', body='$body' $image_sql WHERE id=$post_id";
        $conn->query($sql);

        // Mettre à jour le topic associé
        $conn->query("UPDATE post_topic SET topic_id=$topic_id WHERE post_id=$post_id");

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

		<!-- Middle form - to create and edit  -->
		<div class="action create-post-div">
			<h1 class="page-title">Create/Edit Post</h1>

			<form method="post" enctype="multipart/form-data" action="<?php echo BASE_URL . 'admin/create_post.php'; ?>" >

				<!-- validation errors for the form -->
				<?php if (!empty($errors)): ?>
					<div style="color:red;"><?php foreach($errors as $e) echo $e."<br>"; ?></div>
				<?php endif; ?>

				<!-- if editing post, the id is required to identify that post -->
				<?php if ($isEditingPost === true): ?>
					<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
				<?php endif ?>

				<input 
					type="text"
					name="title"
					value="<?php echo $title; ?>" 
					placeholder="Title">

				<label style="float: left; margin: 5px auto 5px;">Featured image</label>
				<input 
					type="file"
					name="featured_image"
					>

				<textarea name="body" id="body" cols="30" rows="10"><?php echo $body; ?></textarea>
				
				<select name="topic_id">
					<option value="" selected disabled>Choose topic</option>
					<?php foreach ($topics as $topic): ?>
						<option value="<?php echo $topic['id']; ?>" <?php if ($topic_id == $topic['id']) echo "selected"; ?>>
							<?php echo $topic['name']; ?>
						</option>
					<?php endforeach ?>
				</select>
				
				<!-- if editing post, display the update button instead of create button -->
				<?php if ($isEditingPost === true): ?> 
					<button type="submit" class="btn" name="update_post">UPDATE</button>
				<?php else: ?>
					<button type="submit" class="btn" name="create_post">Save Post</button>
				<?php endif ?>

			</form>
		</div>
		<!-- // Middle form - to create and edit -->

	</div>

</body>
</html>

<script>
	CKEDITOR.replace('body');
</script>
