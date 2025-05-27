<?php
include('config.php');
include(ROOT_PATH . '/includes/all_functions.php');
include(ROOT_PATH . '/includes/admin_functions.php'); 
include(ROOT_PATH . '/includes/public/head_section.php');
if (session_status() == PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Admin') {
    include(ROOT_PATH . '/includes/admin/navbar.php');
} else {
    include(ROOT_PATH . '/includes/public/navbar.php');
}

$title = "Filtered Posts";
$filters = [];

// Récupération des filtres depuis l'URL (GET)
if (!empty($_GET['topic_id'])) {
    $filters['topic_id'] = intval($_GET['topic_id']);
}
if (!empty($_GET['author_id'])) {
    $filters['author_id'] = intval($_GET['author_id']);
}
if (!empty($_GET['search'])) {
    $filters['search'] = trim($_GET['search']);
}
$filters['published'] = 1; // Afficher seulement les posts publiés (pour public)

// Récupération des posts filtrés
$posts = getFilteredPosts($filters);

// Pour afficher la liste des topics et auteurs (pour les filtres dans le menu)
$topics = getAllTopics();
foreach ($topics as $t) {
    echo "<a href='?topic_id={$t['id']}'>{$t['name']}</a> ";
}
$authors = getAdminUsers(); 
?>

<title><?php echo $title; ?></title>
</head>
<body>


    <div class="container">
        <h1>Articles filtrés</h1>

        <!-- Barre de filtres -->
        <form method="get" class="filters" style="margin-bottom:20px;">
            <select name="topic_id">
                <option value="">-- Tous les topics --</option>
                <?php foreach ($topics as $topic): ?>
                    <option value="<?php echo $topic['id']; ?>" <?php if(isset($_GET['topic_id']) && $_GET['topic_id'] == $topic['id']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($topic['name']); ?>
                    </option>
                <?php endforeach ?>
            </select>
            <select name="author_id">
                <option value="">-- Tous les auteurs --</option>
                <?php foreach ($authors as $author): ?>
                    <option value="<?php echo $author['id']; ?>" <?php if(isset($_GET['author_id']) && $_GET['author_id'] == $author['id']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($author['username']); ?>
                    </option>
                <?php endforeach ?>
            </select>
            <input type="text" name="search" placeholder="Recherche" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Filtrer</button>
        </form>

        <!-- Affichage des posts -->
        <?php if (empty($posts)): ?>
            <p>Aucun article trouvé avec ces critères.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Topic</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><a href="single_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></td>
                        <td><?php echo htmlspecialchars($post['username']); ?></td>
                        <td><?php echo isset($post['topic']) ? htmlspecialchars($post['topic']) : '<em>Non défini</em>'; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php include(ROOT_PATH . '/includes/public/footer.php'); ?>
</body>
</html>
