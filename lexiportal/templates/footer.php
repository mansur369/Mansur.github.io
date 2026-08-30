</main>
<footer class="site-footer">
  <div class="container">
    <div class="footer-top">
      <div class="footer-brand">
        <div class="brand"><span class="mark">L</span><span>LexPortal</span></div>
        <p>Your gateway to legal knowledge, practice, and justice. Built for Uganda's legal community.</p>
      </div>
      <div class="footer-col"><h5>Research</h5><a href="research.php?type=case">Cases</a><a href="research.php?type=legislation">Legislation</a><a href="resources.php">Journals</a></div>
      <div class="footer-col"><h5>Services</h5><a href="lawyers-directory.php">Lawyer Directory</a><a href="contact.php">Consultation</a><a href="assistant.php">AI Assistant</a></div>
      <div class="footer-col"><h5>Support</h5><a href="contact.php">Help Center</a><a href="#">Privacy Policy</a><a href="#">Terms</a></div>
    </div>
    <div class="footer-bottom">
      <span>&copy; 2026 LexPortal. All rights reserved.</span>
      <select aria-label="Language"><option>English</option><option>Luganda</option><option>Kiswahili</option><option>Arabic</option></select>
    </div>
  </div>
</footer>

<div class="modal-overlay" id="loginModal">
  <div class="modal-box">
    <button class="modal-close" data-close-modal aria-label="Close">×</button>
    <h2>Sign in</h2>
    <p class="modal-sub">Access your client or practitioner dashboard.</p>
    <form method="post" action="login.php">
      <div class="form-group"><label>Email address</label><input type="email" name="email" placeholder="you@example.com" required></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" placeholder="At least 6 characters" required></div>
      <button class="btn btn-primary btn-block">Sign in</button>
      <p class="form-hint">Demo client: client@demo.lexportal.ug / demo1234<br>Demo lawyer: lawyer@demo.lexportal.ug / demo1234</p>
    </form>
    <p class="modal-footer-link">Don't have an account? <a href="#" data-swap-modal="registerModal">Register</a></p>
  </div>
</div>
<div class="modal-overlay" id="registerModal">
  <div class="modal-box">
    <button class="modal-close" data-close-modal aria-label="Close">×</button>
    <h2>Create your account</h2>
    <p class="modal-sub">Join as a client or legal practitioner.</p>
    <form method="post" action="register.php">
      <div class="role-select-row">
        <label class="role-option active"><input type="radio" name="role" value="client" checked> I'm a Client</label>
        <label class="role-option"><input type="radio" name="role" value="practitioner"> I'm a Practitioner</label>
      </div>
      <div class="form-group"><label>Full name</label><input type="text" name="name" placeholder="Jane Mukasa" required></div>
      <div class="form-group"><label>Email address</label><input type="email" name="email" placeholder="you@example.com" required></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" placeholder="At least 6 characters" required></div>
      <button class="btn btn-primary btn-block">Create account</button>
    </form>
    <p class="modal-footer-link">Already have an account? <a href="#" data-swap-modal="loginModal">Sign in</a></p>
  </div>
</div>
<div class="modal-overlay" id="searchModal">
  <div class="modal-box">
    <button class="modal-close" data-close-modal aria-label="Close">×</button>
    <h2>Search LexPortal</h2>
    <p class="modal-sub">Cases, legislation, articles, and more.</p>
    <form action="research.php">
      <div class="search-field"><span>⌕</span><input type="text" name="q" id="globalSearch" placeholder="Search anything..."></div>
      <button class="btn btn-primary btn-block">Search</button>
    </form>
  </div>
</div>
<script src="public/assets/app.js"></script>
<?php if (!empty($_GET['login'])): ?><script>document.getElementById('loginModal').classList.add('show');</script><?php endif; ?>
</body>
</html>
