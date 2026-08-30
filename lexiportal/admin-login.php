<?php
require __DIR__ . '/config/app.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM admins WHERE lower(email) = lower(?)');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        flash('Welcome, ' . $admin['name'] . '.');
        redirect_to('admin.php');
    }
    flash('Incorrect admin email or password.');
}

$page = 'admin';
$title = 'Admin Sign In';
include __DIR__ . '/templates/header.php';
?>
<section class="section section-alt"><div class="container"><div class="section-head left"><p class="eyebrow">Admin</p><h1>Admin sign in</h1><p>Admin accounts are separate from client and practitioner accounts.</p></div><form class="panel" method="post"><div class="form-group"><label>Email address</label><input type="email" name="email" value="admin@lexportal.ug" required></div><div class="form-group"><label>Password</label><input type="password" name="password" placeholder="AdminDemo#2026" required></div><button class="btn btn-primary">Sign in</button><p class="form-hint">Demo admin: admin@lexportal.ug / AdminDemo#2026</p></form></div></section>
<?php include __DIR__ . '/templates/footer.php'; ?>
