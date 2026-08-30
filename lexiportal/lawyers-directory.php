<?php
require __DIR__ . '/config/app.php';
$page = 'practice';
$title = 'Lawyers Directory';
$q = trim($_GET['q'] ?? '');
$stmt = $q === '' ? db()->query('SELECT * FROM lawyers ORDER BY name') : db()->prepare('SELECT * FROM lawyers WHERE name LIKE ? OR specialty LIKE ? OR location LIKE ? ORDER BY name');
if ($q !== '') {
    $stmt->execute(["%$q%", "%$q%", "%$q%"]);
}
$lawyers = $stmt->fetchAll();
include __DIR__ . '/templates/header.php';
?>
<section class="section section-alt"><div class="container"><div class="section-head left"><p class="eyebrow">Lawyers Directory</p><h1>Find verified legal professionals</h1></div><form class="panel" action=""><div class="search-field"><span>⌕</span><input name="q" value="<?= e($q) ?>" placeholder="Search by name, specialty, or location"></div><button class="btn btn-primary">Search</button></form></div></section>
<section class="section"><div class="container"><div class="grid-3"><?php foreach ($lawyers as $lawyer): ?><article class="lawyer-card"><span class="badge badge-verified">Verified</span><h3><?= e($lawyer['name']) ?></h3><p><?= e($lawyer['specialty']) ?></p><div class="resource-meta"><?= e($lawyer['location']) ?> · <?= e($lawyer['email']) ?></div><a class="btn btn-outline btn-sm" href="contact.php?subject=Consultation with <?= urlencode($lawyer['name']) ?>">Request consultation</a></article><?php endforeach; ?></div></div></section>
<?php include __DIR__ . '/templates/footer.php'; ?>
