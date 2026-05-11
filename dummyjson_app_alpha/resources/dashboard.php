<?php
require_once '../backend/auth.php';
requireLogin();
$active = 'dashboard';
$username  = htmlspecialchars($_SESSION['username']);
$full_name = htmlspecialchars($_SESSION['full_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — DummyJSON App</title>
</head>
<body>
<div>
    <div>
        <h2>Welcome back, <span><?= $username ?></span></h2>
        <p>You are logged in as <strong><?= $full_name ?></strong>. Explore the sections below.</p>
    </div>
    
    <div>
        <a href="../landing-page/logout.php">
            <span>Logout</span>
        </a>
    </div>

    <div>
        <h1>Dashboard</h1>
        <p>Quick access to all data sections</p>
    </div>

    <div>
        <a href="products.php">
            <div>📦</div>
            <h3>Products</h3>
            <p>Browse product catalog</p>
        </a>
        <a href="users.php">
            <div>👥</div>
            <h3>Users</h3>
            <p>View users &amp; carts</p>
        </a>
        <a href="users.php">
            <div>🛒</div>
            <h3>Carts</h3>
            <p>Cart data via Users page</p>
        </a>
        <a href="posts.php">
            <div>📝</div>
            <h3>Posts</h3>
            <p>Browse blog posts</p>
        </a>
    </nav>
    </div>

    <p>
        Data is fetched live from <a href="https://dummyjson.com" target="_blank">dummyjson.com</a>
    </p>
</div>
</body>
</html>
