<?php
require_once '../backend/auth.php';
require_once '../backend/database.php';
requireLogin();
$active = 'users';

$userData  = fetchAPI('https://dummyjson.com/users?limit=100');
$users     = $userData['users'] ?? [];

$selected_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$selected_user    = null;
$user_carts       = [];

if ($selected_user_id) {
    foreach ($users as $u) {
        if ($u['id'] === $selected_user_id) {
            $selected_user = $u;
            break;
        }
    }

    $cartData  = fetchAPI('https://dummyjson.com/carts?limit=200');
    $all_carts = $cartData['carts'] ?? [];
    foreach ($all_carts as $cart) {
        if ((int)$cart['userId'] === $selected_user_id) {
            $user_carts[] = $cart;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users — DummyJSON App</title>
</head>
<body>
<div>
    <?php if ($selected_user && $selected_user_id): ?>
    <div>
        <div>
            <div>
                <h2>🛒 Cart for <?= htmlspecialchars($selected_user['firstName'] . ' ' . $selected_user['lastName']) ?></h2>
                <p><?= htmlspecialchars($selected_user['email']) ?></p>
            </div>
            <a href="users.php"> Back to Users</a>
        </div>

        <?php if (empty($user_carts)): ?>
            <>No carts found for this user.</p>
        <?php else: ?>
            <?php foreach ($user_carts as $cart): ?>
            <div>
                <div>
                    <span>Cart ID: #<?= (int)$cart['id'] ?></span>
                </div>
                <div>
                    <div><strong><?= (int)$cart['totalProducts'] ?></strong> Total Products</div>
                    <div><strong><?= (int)$cart['totalQuantity'] ?></strong> Total Items</div>
                    <div><strong>$<?= number_format($cart['total'], 2) ?></strong> Total Amount</div>
                    <?php if (isset($cart['discountedTotal'])): ?>
                    <div><strong>$<?= number_format($cart['discountedTotal'], 2) ?></strong> After Discount</div>
                    <?php endif; ?>
                </div>

                <div>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Item Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart['products'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['title']) ?></td>
                                <td><?= (int)$item['quantity'] ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td>$<?= number_format($item['total'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <div>
        <h1>👥 Users</h1>
        <p>Showing <?= count($users) ?> users — click "View Cart" to see a user's cart</p>
    </div>

    <div>
        <a href="dashboard.php">Go back</a>
    </div>

    <div>
        <input type="text" id="userSearch" placeholder="Search by name or email…" oninput="filterUsers()">
    </div>

    <?php if (empty($users)): ?>
        <div class="alert alert-error">Could not fetch users. Check your internet connection.</div>
    <?php else: ?>
    <div class="grid-2" id="userGrid">
        <?php foreach ($users as $u): ?>
        <?php
            $fname = htmlspecialchars($u['firstName'] ?? '');
            $lname = htmlspecialchars($u['lastName'] ?? '');
            $email = htmlspecialchars($u['email'] ?? '');
            $phone = htmlspecialchars($u['phone'] ?? '');
            $age   = (int)($u['age'] ?? 0);
            $img   = htmlspecialchars($u['image'] ?? '');
            $uid   = (int)$u['id'];
        ?>
        <div class="card" data-search="<?= strtolower("$fname $lname $email") ?>">
            <div class="user-card">
                <img src="<?= $img ?>" alt="<?= $fname ?>" class="user-avatar" loading="lazy">
                <div class="user-info">
                    <h4><?= $fname ?> <?= $lname ?></h4>
                    <p>📧 <?= $email ?></p>
                    <p>📞 <?= $phone ?></p>
                    <p>🎂 Age: <?= $age ?></p>
                    <div style="margin-top:10px;">
                        <a href="users.php?user_id=<?= $uid ?>" class="btn btn-purple btn-sm">🛒 View Cart</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <p id="noUserResults" style="display:none; text-align:center; color:var(--text-muted); padding:2rem;">No users match your search.</p>
    <?php endif; ?>
    <?php endif; ?>

</div>

<script>
function filterUsers() {
    const q = document.getElementById('userSearch').value.toLowerCase();
    const cards = document.querySelectorAll('#userGrid .card');
    let visible = 0;
    cards.forEach(card => {
        const match = card.dataset.search.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('noUserResults').style.display = visible === 0 ? '' : 'none';
}
</script>
</body>
</html>
