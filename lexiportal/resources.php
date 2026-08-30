<?php
require __DIR__ . '/config/app.php';
$page = 'resources';
$title = 'Resources';
$cat = trim($_GET['cat'] ?? '');
$stmt = $cat === '' ? db()->query('SELECT * FROM resources ORDER BY id DESC') : db()->prepare('SELECT * FROM resources WHERE category = ? ORDER BY id DESC');
if ($cat !== '') {
    $stmt->execute([$cat]);
}
$resources = $stmt->fetchAll();
include __DIR__ . '/templates/header.php';
?>
<section class="section section-alt"><div class="container"><div class="section-head"><p class="eyebrow">Knowledge Center</p><h1>Browse by area of law</h1></div><div class="tag-grid">
<?php foreach (['criminal','contract','constitutional','islamic','commercial','family','property','international'] as $area): ?>
  <a class="tag-card" href="resources.php?cat=<?= e($area) ?>"><div><div class="t-name"><?= e(ucfirst($area)) ?> Law</div><div class="t-count">SQLite category</div></div></a>
<?php endforeach; ?>
</div></div></section>
<section class="section"><div class="container"><div class="section-head left"><p class="eyebrow">Resources</p><h2><?= $cat ? e(ucfirst($cat)) . ' law' : 'All articles, notes and templates' ?></h2></div><div class="resource-grid">
<?php foreach ($resources as $resource): ?><article class="resource-card"><div class="resource-type"><?= e($resource['type']) ?></div><h3><?= e($resource['title']) ?></h3><div class="resource-meta"><?= e($resource['meta']) ?></div><p><?= e($resource['body']) ?></p></article><?php endforeach; ?>
</div></div></section>
<?php include __DIR__ . '/templates/footer.php'; ?>
