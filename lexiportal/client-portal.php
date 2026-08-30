<?php
require __DIR__ . '/config/app.php';
$user = require_user('client');
$page = 'client';
$title = 'Client Portal';
$caseStmt = db()->prepare('SELECT * FROM client_cases WHERE client_id = ? ORDER BY id DESC');
$caseStmt->execute([$user['id']]);
$cases = $caseStmt->fetchAll();
$docStmt = db()->prepare('SELECT d.*, c.title AS case_title FROM documents d LEFT JOIN client_cases c ON c.id = d.case_id WHERE d.user_id = ? ORDER BY d.id DESC LIMIT 8');
$docStmt->execute([$user['id']]);
$docs = $docStmt->fetchAll();
$msgStmt = db()->prepare('SELECT * FROM messages WHERE user_id = ? ORDER BY id DESC LIMIT 5');
$msgStmt->execute([$user['id']]);
$messages = $msgStmt->fetchAll();
$activeCases = 0;
$pendingCases = 0;
foreach ($cases as $case) {
    if ($case['status'] === 'Active') {
        $activeCases++;
    }
    if ($case['status'] === 'Pending') {
        $pendingCases++;
    }
}
$unread = 0;
foreach ($messages as $message) {
    if (!$message['is_read']) {
        $unread++;
    }
}
include __DIR__ . '/templates/header.php';
?>
<div class="app-shell">
  <aside class="app-sidebar">
    <span class="role-tag">Client account</span>
    <div class="user-block"><div class="avatar"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></div><div><div class="name"><?= e($user['name']) ?></div><div class="sub"><?= e($user['email']) ?></div></div></div>
    <nav class="side-nav"><a class="active" href="#">Overview</a><a href="#cases">My Cases</a><a href="#documents">Documents</a><a href="#messages">Messages</a><a href="assistant.php">AI Assistant</a><a href="logout.php">Sign out</a></nav>
  </aside>
  <div class="app-main">
    <div class="app-header"><div><h1>Welcome back, <?= e(strtok($user['name'], ' ')) ?></h1><p class="meta">Here's what's happening with your cases today.</p></div><button class="btn btn-primary" data-open-modal="uploadModal">Upload Document</button></div>
    <div class="stat-grid"><div class="stat-card"><div class="stat-value"><?= $activeCases ?></div><div class="stat-label">Active cases</div></div><div class="stat-card"><div class="stat-value"><?= $pendingCases ?></div><div class="stat-label">Pending matters</div></div><div class="stat-card"><div class="stat-value"><?= count($docs) ?></div><div class="stat-label">Documents on file</div></div><div class="stat-card"><div class="stat-value"><?= $unread ?></div><div class="stat-label">Unread messages</div></div></div>
    <div class="panel" id="cases"><div class="panel-head"><h3>My Cases</h3></div><table class="data-table"><thead><tr><th>Case</th><th>Advocate</th><th>Next step</th><th>Status</th></tr></thead><tbody><?php foreach ($cases as $case): ?><tr><td><?= e($case['title']) ?></td><td><?= e($case['advocate']) ?></td><td><?= e($case['next_step']) ?></td><td><span class="status-pill status-<?= strtolower($case['status']) === 'active' ? 'active' : (strtolower($case['status']) === 'pending' ? 'pending' : 'closed') ?>"><?= e($case['status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
    <div class="grid-3" style="grid-template-columns:1fr 1fr;margin-top:18px">
      <div class="panel" id="documents"><div class="panel-head"><h3>Recent Documents</h3></div><?php foreach ($docs as $doc): ?><div class="doc-row"><div class="doc-info"><div class="doc-name"><?= e($doc['original_name']) ?></div><div class="doc-meta"><?= number_format(((int) $doc['size_bytes']) / 1024, 1) ?> KB · <?= e($doc['uploaded_at']) ?><?= $doc['case_title'] ? ' · ' . e($doc['case_title']) : '' ?></div></div></div><?php endforeach; ?></div>
      <div class="panel" id="messages"><div class="panel-head"><h3>Messages</h3></div><?php foreach ($messages as $message): ?><div class="doc-row"><div class="doc-info"><div class="doc-name"><?= e($message['sender']) ?></div><div class="doc-meta"><?= e($message['body']) ?></div></div></div><?php endforeach; ?></div>
    </div>
  </div>
</div>
<div class="modal-overlay" id="uploadModal">
  <div class="modal-box">
    <button class="modal-close" data-close-modal aria-label="Close">×</button>
    <h2>Upload a document</h2>
    <p class="modal-sub">Files are recorded in SQLite and stored in the local uploads folder.</p>
    <form method="post" action="upload-document.php" enctype="multipart/form-data">
      <div class="form-group"><label>Related case</label><select name="case_id"><?php foreach ($cases as $case): ?><option value="<?= (int) $case['id'] ?>"><?= e($case['title']) ?></option><?php endforeach; ?></select></div>
      <div class="form-group"><label>File</label><input type="file" name="document" required><p class="form-hint">PDF, DOC, DOCX, JPG, or PNG up to 25MB.</p></div>
      <button class="btn btn-primary btn-block">Upload</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
