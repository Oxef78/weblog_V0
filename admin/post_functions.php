<?php
if (!isset($conn)) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
}

// --- POSTS FUNCTIONS ---
function getAllPosts() {
    global $conn;
    $sql = "SELECT 
                p.*, 
                u.username, 
                t.name AS topic, 
                t.id AS topic_id
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN post_topic pt ON pt.post_id = p.id
            LEFT JOIN topics t ON pt.topic_id = t.id
            ORDER BY p.created_at DESC";
    $res = $conn->query($sql);
    $posts = [];
    while ($row = $res->fetch_assoc()) $posts[] = $row;
    return $posts;
}
function getPostsByAuthor($user_id) {
    global $conn;
    $sql = "SELECT 
                p.*, 
                u.username, 
                t.name AS topic, 
                t.id AS topic_id
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN post_topic pt ON pt.post_id = p.id
            LEFT JOIN topics t ON pt.topic_id = t.id
            WHERE p.user_id = $user_id
            ORDER BY p.created_at DESC";
    $res = $conn->query($sql);
    $posts = [];
    while ($row = $res->fetch_assoc()) $posts[] = $row;
    return $posts;
}

// Crée un post
function createPost($user_id, $title, $slug, $body, $image, $topic_id, $published=1) {
    global $conn;
    // Gérer l’upload image ici si besoin
    $isAdmin = (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'Admin');
    $published = $isAdmin ? 1 : 0;
    $conn->query("INSERT INTO posts (user_id, title, slug, body, image, published, created_at) VALUES ('$user_id', '$title', '$slug', '$body', '$image', '$published', NOW())");
    $post_id = $conn->insert_id;
    $conn->query("INSERT INTO post_topic (post_id, topic_id) VALUES ('$post_id', '$topic_id')");
}

// Modifie un post
function updatePost($id, $title, $slug, $body, $image, $topic_id, $published=1) {
    global $conn;
    $conn->query("UPDATE posts SET title='$title', slug='$slug', body='$body', image='$image', published='$published' WHERE id=$id");
    // Mettre à jour le post_topic aussi
    $conn->query("UPDATE post_topic SET topic_id='$topic_id' WHERE post_id=$id");
}

// Supprime un post
function deletePost($id) {
    global $conn;
    $conn->query("DELETE FROM posts WHERE id=$id LIMIT 1");
    $conn->query("DELETE FROM post_topic WHERE post_id=$id");
}

?>
