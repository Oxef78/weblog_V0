<?php

$errors = [];
$username = '';

// LOGIN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $pwd = md5($password); // À remplacer par password_hash à terme !

    if (empty($username) || empty($password)) {
        $errors[] = "Both fields required.";
    } else {
        $result = $conn->query("
            SELECT u.id, u.username, r.name AS role
            FROM users u
            LEFT JOIN role_user ru ON u.id = ru.user_id
            LEFT JOIN roles r ON r.id = ru.role_id
            WHERE u.username='$username' AND u.password='$pwd'
            LIMIT 1");
        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            // Redirection intelligente
            if ($user['role'] == 'Admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $errors[] = "Wrong username or password.";
        }
    }
}