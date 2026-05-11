<?php
require_once '../backend/auth.php';
require_once '../backend/database.php';

if (isLoggedIn()) {
    header("Location: ../resources/dashboard.php");
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($full_name)) $errors[] = "Full name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (empty($username) || strlen($username) < 3) $errors[] = "Username must be at least 3 characters.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $conn = getDBConnection();

        // Check uniqueness
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email or username is already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $ins = $conn->prepare("INSERT INTO users (full_name, email, username, password) VALUES (?, ?, ?, ?)");
            $ins->bind_param("ssss", $full_name, $email, $username, $hashed);
            if ($ins->execute()) {
                $success = "Registration successful. You may now login.";
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
            $ins->close();
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — ValoShop</title>
</head>
<body>
<div>
    <div>
        <h2>Create Account</h2>
        <p>Join to access the ValoShop dashboard</p>

        <?php if ($success): ?>
            <div><?= htmlspecialchars($success) ?> &nbsp;<a href="login.php">Login now &rarr;</a></div>
        <?php endif; ?>

        <?php foreach ($errors as $e): ?>
            <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div>
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" placeholder="Juan dela Cruz" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="you@example.com" required>
            </div>
            <div>
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="juandelacruz" required>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" placeholder="Min. 6 characters" required>
            </div>
            <div>
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <?php endif; ?>

        <p>
            Already have an account? <a href="login.php">Login</a>
        </p>
        <p>
            <a href="index.php">Back to home</a>
        </p>
    </div>
</div>
</body>
</html>
