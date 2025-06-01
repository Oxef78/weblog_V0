<?php
include(__DIR__ . '/../../config.php');
if (session_status() == PHP_SESSION_NONE) session_start();
$errors = [];
$username = '';

// LOGIN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $errors[] = "Both fields required.";
    } else {
        // On cherche le user par username
        $stmt = $conn->prepare("
            SELECT u.id, u.username, u.password, r.name AS role
            FROM users u
            LEFT JOIN role_user ru ON u.id = ru.user_id
            LEFT JOIN roles r ON r.id = ru.role_id
            WHERE u.username=? 
            LIMIT 1
        ");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();
            // On vérifie le mot de passe (hash sécurisé)
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ];
                // Redirection selon le rôle
                if ($user['role'] == 'Admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $errors[] = "Wrong username or password.";
            }
        } else {
            $errors[] = "Wrong username or password.";
        }
        $stmt->close();
    }
}
?>
