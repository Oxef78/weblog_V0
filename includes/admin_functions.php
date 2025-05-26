<?php
// --- ADMIN FUNCTIONS ---

// Retourne la liste des rôles (Admin ou Author)
function getAdminRoles() {
    global $conn;
    $result = $conn->query("SELECT id, name AS role FROM roles");
    $roles = [];
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }
    return $roles;
}


// Retourne tous les users qui sont admin ou author
function getAdminUsers() {
    global $conn;
    $sql = "SELECT u.id, u.username, u.email, r.name AS role
            FROM users u
            LEFT JOIN role_user ru ON u.id = ru.user_id
            LEFT JOIN roles r ON ru.role_id = r.id
            ORDER BY u.id";
    $result = $conn->query($sql);
    $admins = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }
    }
    return $admins;
}




// Crée un utilisateur admin (utilisé lors du POST create_admin)
function createAdmin($username, $email, $role_id, $password) {
    global $conn;

    $password = md5($password);

    $query = "INSERT INTO users (username, email, password, created_at) VALUES ('$username', '$email', '$password', NOW())";
    $conn->query($query);

    $user_id = $conn->insert_id;

    $conn->query("INSERT INTO role_user (user_id, role_id) VALUES ($user_id, $role_id)");
}


// Modifie un utilisateur admin
function updateAdmin($admin_id, $username, $email, $role_id, $password) {
    global $conn;
    // Si un mot de passe est fourni, le mettre à jour
    $setPassword = $password ? ", password='" . md5($password) . "'" : "";

    $conn->query("UPDATE users SET username='$username', email='$email' $setPassword WHERE id=$admin_id");
    $conn->query("UPDATE role_user SET role_id=$role_id WHERE user_id=$admin_id");
}


// Supprime un utilisateur admin
function deleteAdmin($admin_id) {
    global $conn;
    // Supprime le lien rôle
    $conn->query("DELETE FROM role_user WHERE user_id=$admin_id");
    // Supprime l'utilisateur
    $conn->query("DELETE FROM users WHERE id=$admin_id");
}


// --- TOPIC FUNCTIONS ---

// Récupérer tous les topics
function getAllTopics() {
    global $conn;
    $topics = [];
    $res = $conn->query("SELECT * FROM topics");
    while ($row = $res->fetch_assoc()) $topics[] = $row;
    return $topics;
}

// Créer un topic
function createTopic($name, $slug) {
    global $conn;
    $conn->query("INSERT INTO topics (name, slug) VALUES ('$name', '$slug')");
}

// Modifier un topic
function updateTopic($id, $name, $slug) {
    global $conn;
    $conn->query("UPDATE topics SET name='$name', slug='$slug' WHERE id=$id");
}

// Supprimer un topic
function deleteTopic($id) {
    global $conn;
    $conn->query("DELETE FROM topics WHERE id=$id LIMIT 1");
}


?>
