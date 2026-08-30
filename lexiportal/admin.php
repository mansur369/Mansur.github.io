<?php
require __DIR__ . '/config/app.php';
$admin = require_admin();
$page = 'admin';
$title = 'Admin Snapshot';
$counts = [
    'Users' => db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'Research records' => db()->query('SELECT COUNT(*) FROM research_items')->fetchColumn(),
    'Resources' => db()->query('SELECT COUNT(*) FROM resources')->fetchColumn(),
    'Contact requests' => db()->query('SELECT COUNT(*) FROM contact_requests')->fetchColumn(),
    'AI questions' => db()->query('SELECT COUNT(*) FROM ai_questions')->fetchColumn(),
];
$contacts = db()->query('SELECT * FROM contact_requests ORDER BY id DESC LIMIT 10')->fetchAll();
include __DIR__ . '/templates/header.php';
?>
<section class="section section-alt"><div class="container"><div class="section-head left"><p class="eyebrow">Admin</p><h1>SQLite application snapshot</h1><p>Signed in as <?= e($admin['name']) ?>. <a class="card-cta" href="admin-logout.php">Admin sign out</a></p></div><div class="stat-grid"><?php foreach ($counts as $label => $count): ?><div class="stat-card"><div class="stat-value"><?= e((string) $count) ?></div><div class="stat-label"><?= e($label) ?></div></div><?php endforeach; ?></div><div class="panel"><h3>Recent contact requests</h3><table class="data-table"><thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Date</th></tr></thead><tbody><?php foreach ($contacts as $contact): ?><tr><td><?= e($contact['name']) ?></td><td><?= e($contact['email']) ?></td><td><?= e($contact['subject']) ?></td><td><?= e($contact['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></section>
<?php include __DIR__ . '/templates/footer.php'; ?>
