<?php
require_once '../backend/auth.php';
require_once '../backend/database.php';
requireLogin();     
$active = 'products';

// Fetch products from DummyJSON
$data = fetchAPI('https://dummyjson.com/products?limit=100');
$products = $data['products'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products — DummyJSON App</title>
</head>
<body>
<div>
    <div>
        <h1>📦 Products</h1>
        <p>Showing <?= count($products) ?> products from DummyJSON</p>
    </div>

    <div>
        <a href="dashboard.php">Go back</a>
    </div>
    
    <div>
        <input type="text" id="searchInput" placeholder="Search products by name or category…" oninput="filterProducts()">
    </div>

    <?php if (empty($products)): ?>
        <div>Could not fetch products. Check your internet connection.</div>
    <?php else: ?>
    <div class="product-grid" id="productGrid">
        <?php foreach ($products as $p): ?>
        <div class="card product-card" data-name="<?= strtolower(htmlspecialchars($p['title'])) ?>" data-cat="<?= strtolower(htmlspecialchars($p['category'])) ?>">
            <img src="<?= htmlspecialchars($p['thumbnail'] ?? '') ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
            <h4><?= htmlspecialchars($p['title']) ?></h4>
            <div>$<?= number_format($p['price'], 2) ?></div>
            <div>
                <span><?= htmlspecialchars($p['category']) ?></span>
                <span>Stock: <?= (int)($p['stock']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <p id="noResults" style="display:none;">No products match your search.</p>
    <?php endif; ?>
</div>

<script>
function filterProducts() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('#productGrid .product-card');
    let visible = 0;
    cards.forEach(card => {
        const match = card.dataset.name.includes(q) || card.dataset.cat.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('noResults').style.display = visible === 0 ? '' : 'none';
}
</script>
</body>
</html>
