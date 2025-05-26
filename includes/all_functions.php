<?php
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

function getPublishedPosts() {
    global $conn;
    $sql = "SELECT * FROM posts WHERE published = 1 ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

function getPostById($id) {
    global $conn;
    $id = intval($id); // sécurité de base
    $sql = "SELECT * FROM posts WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

/**
 * Récupère les posts filtrés (topic, auteur, mot-clé dans le titre, published, etc.)
 * $filters = ['topic_id' => 2, 'author_id' => 5, 'search' => 'mot', ...]
 */
function getFilteredPosts($filters = []) {
    global $conn;

    $sql = "SELECT p.*, u.username, t.name as topic
            FROM posts p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN post_topic pt ON pt.post_id = p.id
            LEFT JOIN topics t ON pt.topic_id = t.id
            WHERE 1 ";

    // Ajoute dynamiquement les filtres SANS refaire de JOIN
    if (!empty($filters['topic_id'])) {
        $topic_id = intval($filters['topic_id']);
        $sql .= " AND pt.topic_id = $topic_id ";
    }
    if (!empty($filters['author_id'])) {
        $author_id = intval($filters['author_id']);
        $sql .= " AND p.user_id = $author_id ";
    }
    if (isset($filters['published'])) {
        $published = intval($filters['published']);
        $sql .= " AND p.published = $published ";
    }
    $sql .= "ORDER BY p.created_at DESC";

    $result = $conn->query($sql);
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

// Dans admin_functions.php ou all_functions.php
function getAllUsers() {
    global $conn;
    $result = $conn->query("SELECT id, username FROM users ORDER BY username");
    $users = [];
    while ($row = $result->fetch_assoc()) $users[] = $row;
    return $users;
}



?>

