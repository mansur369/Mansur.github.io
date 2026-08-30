<?php
require __DIR__ . '/config/app.php';
$page = 'contact';
$title = 'Contact';
$subject = trim($_GET['subject'] ?? '');
include __DIR__ . '/templates/header.php';
?>
<section class="section section-alt"><div class="container"><div class="section-head left"><p class="eyebrow">Contact</p><h1>Get legal support or platform help</h1><p>Submissions are stored in SQLite for follow-up.</p></div><form class="panel" method="post" action="contact-save.php"><div class="grid-3"><div class="form-group"><label>Name</label><input name="name" required></div><div class="form-group"><label>Email</label><input type="email" name="email" required></div><div class="form-group"><label>Subject</label><input name="subject" value="<?= e($subject) ?>" required></div></div><div class="form-group"><label>Message</label><textarea name="message" rows="6" required></textarea></div><button class="btn btn-primary">Send request</button></form></div></section>
<?php include __DIR__ . '/templates/footer.php'; ?>
