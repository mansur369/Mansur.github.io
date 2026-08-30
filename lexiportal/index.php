<?php
require __DIR__ . '/config/app.php';
$page = 'home';
$title = 'Your Gateway to Legal Knowledge, Practice, and Justice';
$research = db()->query('SELECT * FROM research_items ORDER BY verified DESC, year DESC LIMIT 3')->fetchAll();
$resources = db()->query('SELECT * FROM resources ORDER BY id DESC LIMIT 4')->fetchAll();
include __DIR__ . '/templates/header.php';
?>
<section class="hero">
  <div class="container">
    <div>
      <p class="eyebrow hero-eyebrow">Uganda's Legal Research & Practice Platform</p>
      <h1>Access Legal Knowledge <span class="accent">Faster Than Ever</span></h1>
      <p class="lead">Research laws, cases, and precedents, get AI-assisted answers grounded in Ugandan legislation, and connect with verified legal professionals, all in one platform.</p>
      <div class="hero-cta-row">
        <a class="btn btn-gold btn-lg" href="research.php">Start Research</a>
        <a class="btn btn-outline-light btn-lg" href="assistant.php">Ask the AI Assistant</a>
      </div>
      <div class="hero-trust">
        <div><span class="num">12,400+</span><span class="label">Court decisions indexed</span></div>
        <div><span class="num">340+</span><span class="label">Acts and statutes</span></div>
        <div><span class="num">850+</span><span class="label">Verified advocates</span></div>
        <div><span class="num">99.9%</span><span class="label">Demo uptime target</span></div>
      </div>
    </div>
    <div class="quick-search-card">
      <h3>Quick Search</h3>
      <p class="sub">Search across cases, legislation, and articles.</p>
      <div class="qs-tabs"><button class="qs-tab active" type="button">Cases</button><button class="qs-tab" type="button">Legislation</button><button class="qs-tab" type="button">Articles</button></div>
      <form action="research.php">
        <div class="search-field"><span>⌕</span><input type="text" name="q" id="qsCases" placeholder="Search cases, e.g. Uganda v. Kato"></div>
        <button type="button" class="btn btn-outline btn-block" data-voice-search="#qsCases">Use voice search</button>
        <button class="btn btn-primary btn-block" style="margin-top:10px">Search</button>
      </form>
    </div>
  </div>
</section>
<section class="trust-band"><div class="container"><strong>Trusted sources</strong><div class="trust-logos"><span class="trust-logo-item">Uganda Law Society</span><span class="trust-logo-item">Judiciary of Uganda</span><span class="trust-logo-item">URSB</span><span class="trust-logo-item">Parliament of Uganda</span></div></div></section>
<section class="section">
  <div class="container">
    <div class="section-head"><p class="eyebrow">What you can do here</p><h2>Everything you need, in one platform</h2><p>Research, case tracking, secure documents, messaging, and a practical legal assistant.</p></div>
    <div class="grid-4">
      <div class="dash-card"><h3>Legal Research</h3><p class="desc">Search case law and statutes with full-text filters.</p><a class="card-cta" href="research.php">Search now</a></div>
      <div class="dash-card"><h3>Court Decisions</h3><p class="desc">Browse judgments from courts across Uganda.</p><a class="card-cta" href="research.php?type=case">View judgments</a></div>
      <div class="dash-card"><h3>Legislation Hub</h3><p class="desc">Browse the Constitution, Acts, Regulations, and Bills.</p><a class="card-cta" href="research.php?type=legislation">Browse hub</a></div>
      <div class="dash-card"><h3>AI Legal Assistant</h3><p class="desc">Get grounded answers, summaries, and source prompts.</p><a class="card-cta" href="assistant.php">Ask LexAI</a></div>
    </div>
  </div>
</section>
<section class="section section-alt">
  <div class="container">
    <div class="section-head left"><p class="eyebrow">Research Center preview</p><h2>Find precedent in seconds, not hours</h2></div>
    <div class="research-layout">
      <aside class="filter-panel"><h4>Filter results</h4><div class="filter-group"><label class="filter-label">Year</label><div class="chip-row"><button class="chip active">2026</button><button class="chip">2025</button><button class="chip">2024</button><button class="chip">Older</button></div></div><a class="btn btn-primary btn-block btn-sm" href="research.php">View full Research Center</a></aside>
      <div>
        <?php foreach ($research as $item): ?>
          <article class="result-card"><div class="result-card-top"><h4><?= e($item['title']) ?></h4><span class="badge badge-verified">Verified</span></div><div class="result-meta"><span class="badge badge-court"><?= e($item['court']) ?></span><span><?= e((string) $item['year']) ?></span><span><?= e($item['citation']) ?></span></div><p><?= e($item['summary']) ?></p><div class="result-actions"><a class="btn btn-outline btn-sm" href="research.php?q=<?= urlencode($item['title']) ?>">Read summary</a><button class="btn btn-ghost btn-sm">Download PDF</button></div></article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="section-head"><p class="eyebrow">For clients</p><h2>Track your case, securely</h2><p>A private dashboard to follow status, view documents, and message your advocate.</p></div>
    <div class="grid-3"><div class="dash-card"><h3>My Cases</h3><p class="desc">Real-time status on every active matter.</p></div><div class="dash-card"><h3>Documents</h3><p class="desc">Securely upload and access case files.</p></div><div class="dash-card"><h3>Messages</h3><p class="desc">Direct messaging with your advocate.</p></div></div>
  </div>
</section>
<section class="section section-alt">
  <div class="container">
    <div class="section-head"><p class="eyebrow">Latest resources</p><h2>Articles, notes and templates</h2></div>
    <div class="resource-grid">
      <?php foreach ($resources as $resource): ?>
        <article class="resource-card"><div class="resource-type"><?= e($resource['type']) ?></div><h4><?= e($resource['title']) ?></h4><div class="resource-meta"><?= e($resource['meta']) ?></div></article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php include __DIR__ . '/templates/footer.php'; ?>
