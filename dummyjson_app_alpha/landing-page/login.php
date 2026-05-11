<?php
require_once '../backend/auth.php';
require_once '../backend/database.php';

if (isLoggedIn()) {
    header("Location: ../resources/dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, full_name, username, password FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['full_name']  = $user['full_name'];
                header("Location: ../resources/dashboard.php");
                exit();
            } else {
                $error = "Invalid username/email or password.";
            }
        } else {
            $error = "Invalid username/email or password.";
        }
        $stmt->close();
        $conn->close();
    }
}

$redirect_msg = htmlspecialchars($_GET['error'] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ValoShop</title>
</head>
<body>
<div>
    <div>
        <h2>Welcome Back</h2>
        <p>Sign in to access your dashboard</p>

        <?php if ($redirect_msg): ?>
            <div><?= $redirect_msg ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Username or Email</label>
                <input type="text" name="login" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" placeholder="Enter username or email" required autofocus>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <p>
            No account yet? <a href="register.php">Register</a>
        </p>
        <p>
            <a href="index.php">Back to home</a>
        </p>
    </div>
</div>
</body>
</html>
