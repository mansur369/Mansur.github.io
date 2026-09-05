<?php
require __DIR__ . '/config/app.php';
$page = 'research';
$title = 'Research Center';
$user = current_user();

$q = trim($_GET['q'] ?? '');
$type = trim($_GET['type'] ?? '');
$subject = trim($_GET['subject'] ?? '');

// --- Primary sources: case law & legislation/statutes ---
$sql = 'SELECT * FROM research_items WHERE 1=1';
$params = [];
if ($q !== '') {
    $sql .= ' AND (title LIKE ? OR summary LIKE ? OR citation LIKE ? OR court LIKE ? OR subject LIKE ? OR type LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%", "%$q%", "%$q%");
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
$primaryItems = $stmt->fetchAll();

// --- Secondary sources: books, notes, articles, templates, guides ---
$secondaryItems = [];
if ($type === '' || $type === 'resource') {
    $rsql = 'SELECT * FROM resources WHERE 1=1';
    $rparams = [];
    if ($q !== '') {
        $rsql .= ' AND (title LIKE ? OR body LIKE ? OR meta LIKE ? OR category LIKE ? OR type LIKE ?)';
        array_push($rparams, "%$q%", "%$q%", "%$q%", "%$q%", "%$q%");
    }
    if ($subject !== '') {
        $rsql .= ' AND category = ?';
        $rparams[] = $subject;
    }
    $rsql .= ' ORDER BY id DESC';
    $rstmt = db()->prepare($rsql);
    $rstmt->execute($rparams);
    $secondaryItems = $rstmt->fetchAll();
}

$totalCount = count($primaryItems) + count($secondaryItems);

// --- Save search history (logged-in users only) ---
if ($q !== '' && $user) {
    $hstmt = db()->prepare('INSERT INTO search_history (user_id, query) VALUES (?, ?)');
    $hstmt->execute([$user['id'], $q]);
}

// --- Load recent history for sidebar ---
$history = [];
if ($user) {
    $hstmt = db()->prepare('SELECT * FROM search_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 15');
    $hstmt->execute([$user['id']]);
    $history = $hstmt->fetchAll();
}

include __DIR__ . '/templates/header.php';
?>
<section class="section section-alt">
  <div class="container">
    <div class="section-head left"><p class="eyebrow">Research Center</p><h1>Cases, legislation and reference materials</h1><p>Search draws first from primary sources (case law and legislation), then from books and notes.</p></div>
    <div class="research-layout">
      <aside class="filter-panel">
        <form>
          <div class="form-group"><label>Keyword</label><input name="q" value="<?= e($q) ?>" placeholder="case, statute, citation, topic"></div>
          <div class="form-group"><label>Type</label><select name="type"><option value="">All</option><option value="case" <?= $type === 'case' ? 'selected' : '' ?>>Cases</option><option value="legislation" <?= $type === 'legislation' ? 'selected' : '' ?>>Legislation</option><option value="resource" <?= $type === 'resource' ? 'selected' : '' ?>>Books & Notes</option></select></div>
          <div class="form-group"><label>Subject</label><select name="subject"><option value="">All</option><option value="criminal" <?= $subject === 'criminal' ? 'selected' : '' ?>>Criminal</option><option value="property" <?= $subject === 'property' ? 'selected' : '' ?>>Property</option><option value="contract" <?= $subject === 'contract' ? 'selected' : '' ?>>Contract</option><option value="constitutional" <?= $subject === 'constitutional' ? 'selected' : '' ?>>Constitutional</option></select></div>
          <button class="btn btn-primary btn-block">Apply filters</button>
        </form>

        <?php if ($user): ?>
        <div class="panel" style="margin-top:18px">
          <h4>Recent searches</h4>
          <?php if (!$history): ?>
            <p class="form-hint">Your searches will appear here.</p>
          <?php else: ?>
          <form method="post" action="search-history-delete.php">
            <?php foreach ($history as $h): ?>
              <label style="display:flex;align-items:center;gap:8px;padding:6px 0">
                <input type="checkbox" name="ids[]" value="<?= (int) $h['id'] ?>">
                <a href="research.php?q=<?= urlencode($h['query']) ?>" style="flex:1"><?= e($h['query']) ?></a>
              </label>
            <?php endforeach; ?>
            <button class="btn btn-ghost btn-sm" style="margin-top:8px">Clear selected</button>
          </form>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </aside>
      <div>
        <div class="results-toolbar">Showing <strong><?= $totalCount ?></strong> result<?= $totalCount === 1 ? '' : 's' ?></div>

        <?php if ($primaryItems): ?>
        <h3 class="results-group-heading">Primary sources — Case law & Legislation</h3>
        <?php foreach ($primaryItems as $item): ?>
          <article class="result-card"><div class="result-card-top"><h3><?= e($item['title']) ?></h3><span class="badge badge-verified">Verified</span></div><div class="result-meta"><span class="badge badge-court"><?= e($item['court']) ?></span><span><?= e((string) $item['year']) ?></span><span><?= e($item['citation']) ?></span><span><?= e(ucfirst($item['subject'])) ?></span></div><p><?= e($item['summary']) ?></p><div class="result-actions"><a class="btn btn-outline btn-sm" href="assistant.php?prompt=<?= urlencode('Summarize ' . $item['title']) ?>">Ask LexAI</a><button class="btn btn-ghost btn-sm">Download PDF</button></div></article>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($secondaryItems): ?>
        <h3 class="results-group-heading">Books & Notes</h3>
        <?php foreach ($secondaryItems as $item): ?>
          <article class="result-card"><div class="result-card-top"><h3><?= e($item['title']) ?></h3><span class="badge"><?= e($item['type']) ?></span></div><div class="result-meta"><span><?= e($item['meta']) ?></span><span><?= e(ucfirst($item['category'])) ?></span></div><p><?= e($item['body']) ?></p></article>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!$primaryItems && !$secondaryItems): ?><div class="panel"><h3>No matching records</h3><p>Try a broader search term or clear one of the filters.</p></div><?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__ . '/templates/footer.php'; ?>
