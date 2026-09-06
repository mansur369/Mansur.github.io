// ==================== FILE: admin-resources.php ====================

<?php
require __DIR__ . '/config/app.php';
$admin = require_admin();
$page = 'admin';
$title = 'Manage Resources';

$editItem = null;
if (!empty($_GET['id'])) {
    $stmt = db()->prepare('SELECT * FROM resources WHERE id = ?');
    $stmt->execute([(int) $_GET['id']]);
    $editItem = $stmt->fetch() ?: null;
}

$items = db()->query('SELECT * FROM resources ORDER BY id DESC')->fetchAll();

include __DIR__ . '/templates/header.php';
?>
<section class="section section-alt"><div class="container">
  <div class="section-head left">
    <p class="eyebrow">Admin</p>
    <h1>Manage Resources</h1>
    <p>Books, notes, guides and templates searched from the Research Center. <a class="card-cta" href="admin.php">Back to snapshot</a></p>
  </div>

  <div class="panel" style="margin-bottom:28px">
    <h3><?= $editItem ? 'Edit resource #' . (int) $editItem['id'] : 'Add a new resource' ?></h3>
    <form method="post" action="admin-resources-save.php">
      <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int) $editItem['id'] ?>"><?php endif; ?>
      <div class="form-group"><label>Content type</label><input name="type" required value="<?= e($editItem['type'] ?? '') ?>" placeholder="Article, Guide, Notes, Template, Video lecture..."></div>
      <div class="form-group"><label>Title</label><input name="title" required value="<?= e($editItem['title'] ?? '') ?>"></div>
      <div class="form-group"><label>Category</label>
        <select name="category">
          <?php foreach (['criminal','contract','constitutional','islamic','commercial','family','property','international'] as $c): ?>
            <option value="<?= $c ?>" <?= ($editItem['category'] ?? '') === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Meta (e.g. "6 min read")</label><input name="meta" required value="<?= e($editItem['meta'] ?? '') ?>"></div>
      <div class="form-group"><label>Body / description</label><textarea name="body" rows="3"><?= e($editItem['body'] ?? '') ?></textarea></div>
      <button class="btn btn-primary"><?= $editItem ? 'Save changes' : 'Add resource' ?></button>
      <?php if ($editItem): ?><a class="btn btn-ghost" href="admin-resources.php">Cancel</a><?php endif; ?>
    </form>
  </div>

  <div class="panel">
    <h3>All resources (<?= count($items) ?>)</h3>
    <table class="data-table">
      <thead><tr><th>Title</th><th>Type</th><th>Category</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><?= e($item['title']) ?></td>
          <td><?= e($item['type']) ?></td>
          <td><?= e(ucfirst($item['category'])) ?></td>
          <td>
            <a class="card-cta" href="admin-resources.php?id=<?= (int) $item['id'] ?>">Edit</a>
            &nbsp;|&nbsp;
            <form method="post" action="admin-resources-delete.php" style="display:inline" onsubmit="return confirm('Delete this resource?');">
              <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
              <button class="card-cta" style="background:none;border:none;color:#b42318;cursor:pointer;padding:0">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div></section>
<?php include __DIR__ . '/templates/footer.php'; ?>



// ==================== FILE: admin-resources-save.php ====================

<?php
require __DIR__ . '/config/app.php';
$admin = require_admin();

$id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
$type = trim($_POST['type'] ?? '');
$title = trim($_POST['title'] ?? '');
$category = trim($_POST['category'] ?? '');
$meta = trim($_POST['meta'] ?? '');
$body = trim($_POST['body'] ?? '');

if ($type === '' || $title === '' || $meta === '') {
    flash('Content type, title, and meta are required.');
    redirect_to($id ? "admin-resources.php?id=$id" : 'admin-resources.php');
}

if ($id) {
    $stmt = db()->prepare('UPDATE resources SET type=?, title=?, category=?, meta=?, body=? WHERE id=?');
    $stmt->execute([$type, $title, $category, $meta, $body, $id]);
    flash('Resource updated.');
} else {
    $stmt = db()->prepare('INSERT INTO resources (type, title, category, meta, body) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$type, $title, $category, $meta, $body]);
    flash('Resource added.');
}

redirect_to('admin-resources.php');



// ==================== FILE: admin-resources-delete.php ====================

<?php
require __DIR__ . '/config/app.php';
$admin = require_admin();

$id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
if ($id) {
    $stmt = db()->prepare('DELETE FROM resources WHERE id = ?');
    $stmt->execute([$id]);
    flash('Resource deleted.');
}

redirect_to('admin-resources.php');
