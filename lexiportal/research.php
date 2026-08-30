<?php
require __DIR__ . '/config/app.php';
$page = 'research';
$title = 'Research Center';
$q = trim($_GET['q'] ?? '');
$type = trim($_GET['type'] ?? '');
$subject = trim($_GET['subject'] ?? '');
$sql = 'SELECT * FROM research_items WHERE 1=1';
$params = [];
if ($q !== '') {
    $sql .= ' AND (title LIKE ? OR summary LIKE ? OR citation LIKE ? OR court LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%");
}
if ($type !== '') {
    $sql .= ' AND type = ?';
    $params[] = $type;
}
if ($subject !== '') {
    $sql .= ' AND subject = ?';
    $params[] = $subject;
}
$sql .= ' ORDER BY year DESC, title';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();
include __DIR__ . '/templates/header.php';
?>
<section class="section section-alt">
  <div class="container">
    <div class="section-head left"><p class="eyebrow">Research Center</p><h1>Cases and legislation</h1><p>Search seeded Ugandan legal demo records from the SQLite database.</p></div>
    <div class="research-layout">
      <aside class="filter-panel">
        <form>
          <div class="form-group"><label>Keyword</label><input name="q" value="<?= e($q) ?>" placeholder="case, statute, citation"></div>
          <div class="form-group"><label>Type</label><select name="type"><option value="">All</option><option value="case" <?= $type === 'case' ? 'selected' : '' ?>>Cases</option><option value="legislation" <?= $type === 'legislation' ? 'selected' : '' ?>>Legislation</option></select></div>
          <div class="form-group"><label>Subject</label><select name="subject"><option value="">All</option><option value="criminal">Criminal</option><option value="property">Property</option><option value="contract">Contract</option><option value="constitutional">Constitutional</option></select></div>
          <button class="btn btn-primary btn-block">Apply filters</button>
        </form>
      </aside>
      <div>
        <div class="results-toolbar">Showing <strong><?= count($items) ?></strong> result<?= count($items) === 1 ? '' : 's' ?></div>
        <?php foreach ($items as $item): ?>
          <article class="result-card"><div class="result-card-top"><h3><?= e($item['title']) ?></h3><span class="badge badge-verified">Verified</span></div><div class="result-meta"><span class="badge badge-court"><?= e($item['court']) ?></span><span><?= e((string) $item['year']) ?></span><span><?= e($item['citation']) ?></span><span><?= e(ucfirst($item['subject'])) ?></span></div><p><?= e($item['summary']) ?></p><div class="result-actions"><a class="btn btn-outline btn-sm" href="assistant.php?prompt=<?= urlencode('Summarize ' . $item['title']) ?>">Ask LexAI</a><button class="btn btn-ghost btn-sm">Download PDF</button></div></article>
        <?php endforeach; ?>
        <?php if (!$items): ?><div class="panel"><h3>No matching records</h3><p>Try a broader search term or clear one of the filters.</p></div><?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/templates/footer.php'; ?>
