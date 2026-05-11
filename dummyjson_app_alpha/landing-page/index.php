<?php
require_once '../backend/auth.php';
if (isLoggedIn()) {
    header("Location: ../resources/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ValoShop</title>
</head>
<body>
<div>
    <div>
        <p>Integrative Programming Final Project</p>
    </div>

    <h1>ValoShop<br>DummyJSON <span>API</span><br>Web Application</h1>

    <p>A full-stack PHP &amp; MySQL application integrated with the DummyJSON REST API — browse products, users, carts, and posts securely.</p>

    <div>
        <div><span></span> Products Catalog</div>
        <div><span></span> User Profiles</div>
        <div><span></span> Cart Integration</div>
        <div><span></span> Blog Posts</div>
    </div>

    <div >
        <a href="login.php">Login</a>
        <a href="register.php">Create Account</a>
    </div>
</div>
</body>
</html>
