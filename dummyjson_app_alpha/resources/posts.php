<?php
require_once '../backend/auth.php';
require_once '../backend/database.php';
requireLogin();
$active = 'posts';

$data  = fetchAPI('https://dummyjson.com/posts?limit=100');
$posts = $data['posts'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts — DummyJSON App</title>
</head>
<body>
<div>
    <div>
        <h1>📝 Posts</h1>
        <p>Showing <?= count($posts) ?> posts from DummyJSON</p>
    </div>

    <div>
        <a href="dashboard.php">Go back</a>
    </div>

    <div>
        <input type="text" id="postSearch" placeholder="Search posts by title or tag…" oninput="filterPosts()">
    </div>

    <?php if (empty($posts)): ?>
        <div class="alert alert-error">Could not fetch posts. Check your internet connection.</div>
    <?php else: ?>
    <div class="post-grid" id="postGrid">
        <?php foreach ($posts as $post): ?>
        <?php
            $tags = $post['tags'] ?? [];
            $tagsStr = strtolower(implode(' ', $tags));
        ?>
        <div class="card post-card" data-search="<?= strtolower(htmlspecialchars($post['title'])) . ' ' . $tagsStr ?>">
            <h4><?= htmlspecialchars($post['title']) ?></h4>
            <p><?= htmlspecialchars($post['body']) ?></p>

            <?php if (!empty($tags)): ?>
            <div>
                <?php foreach ($tags as $tag): ?>
                <span>#<?= htmlspecialchars($tag) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div>
                <?php if (isset($post['reactions'])): ?>
                    <?php if (is_array($post['reactions'])): ?>
                        <span>👍 <?= (int)($post['reactions']['likes'] ?? 0) ?></span>
                        <span>👎 <?= (int)($post['reactions']['dislikes'] ?? 0) ?></span>
                    <?php else: ?>
                        <span>❤️ <?= (int)$post['reactions'] ?></span>
                    <?php endif; ?>
                <?php endif; ?>
                <span>👁 <?= (int)($post['views'] ?? 0) ?> views</span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <p id="noPostResults" style="display:none;">No posts match your search.</p>
    <?php endif; ?>
</div>

<script>
function filterPosts() {
    const q = document.getElementById('postSearch').value.toLowerCase();
    const cards = document.querySelectorAll('#postGrid .post-card');
    let visible = 0;
    
    cards.forEach(card => {
        const match = card.dataset.search.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    const noResults = document.getElementById('noPostResults');
        if (noResults) {
            noResults.style.display = visible === 0 ? '' : 'none';
        }
}
</script>
</body>
</html>
