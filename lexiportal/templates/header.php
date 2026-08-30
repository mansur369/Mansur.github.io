<?php
$user = current_user();
$page = $page ?? '';
$title = $title ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> - LexPortal</title>
  <meta name="description" content="LexPortal is Uganda's legal research, practice, and justice platform.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/assets/app.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<div class="topbar">
  <div class="container">
    <div class="topbar-trust">
      <span>Verified legal sources</span>
      <span>Uganda focused</span>
      <span>Light SQLite demo</span>
    </div>
    <div class="topbar-links">
      <a href="contact.php">Help</a>
      <a href="admin.php">Admin</a>
    </div>
  </div>
</div>
<header class="navbar">
  <div class="container">
    <a href="index.php" class="brand">
      <span class="mark">L</span>
      <span>LexPortal<span class="tagline-mini">Knowledge · Practice · Justice</span></span>
    </a>
    <button class="nav-toggle" aria-label="Toggle menu" aria-expanded="false">☰</button>
    <nav class="nav-main" aria-label="Primary">
      <a href="index.php" class="nav-link<?= active('home', $page) ?>">Home</a>
      <div class="nav-item has-dropdown">
        <a href="research.php" class="nav-link<?= active('research', $page) ?>">Research</a>
        <div class="dropdown">
          <a href="research.php?type=case"><strong>Court Decisions</strong><span>Supreme Court, Court of Appeal, High Court</span></a>
          <a href="research.php?type=legislation"><strong>Legislation Hub</strong><span>Constitution, Acts, Regulations, Bills</span></a>
        </div>
      </div>
      <div class="nav-item has-dropdown">
        <a href="assistant.php" class="nav-link<?= active('practice', $page) ?>">Practice</a>
        <div class="dropdown">
          <a href="assistant.php"><strong>AI Legal Assistant</strong><span>Research, summaries, drafting and citations</span></a>
          <a href="lawyers-directory.php"><strong>Lawyers Directory</strong><span>Find verified advocates</span></a>
          <a href="practitioner-dashboard.php"><strong>Practitioner Dashboard</strong><span>Case and client tools</span></a>
        </div>
      </div>
      <a href="resources.php" class="nav-link<?= active('resources', $page) ?>">Resources</a>
      <a href="contact.php" class="nav-link<?= active('contact', $page) ?>">Contact</a>
    </nav>
    <div class="nav-actions">
      <button class="nav-search-btn" aria-label="Search" data-open-modal="searchModal">⌕</button>
      <button class="theme-toggle" aria-label="Toggle dark mode">◐</button>
      <?php if ($user): ?>
        <span class="hello">Hi, <?= e(strtok($user['name'], ' ')) ?></span>
        <a href="<?= $user['role'] === 'practitioner' ? 'practitioner-dashboard.php' : 'client-portal.php' ?>" class="btn btn-outline btn-sm">Dashboard</a>
        <a href="logout.php" class="btn btn-ghost btn-sm">Sign out</a>
      <?php else: ?>
        <button class="btn btn-outline btn-sm" data-open-modal="loginModal">Sign In</button>
        <button class="btn btn-primary btn-sm" data-open-modal="registerModal">Register</button>
      <?php endif; ?>
    </div>
  </div>
</header>
<?php if ($notice = flash()): ?>
  <div class="flash"><?= e($notice) ?></div>
<?php endif; ?>
<main id="main">
