(function () {
  const savedTheme = localStorage.getItem('lexportal_theme');
  if (savedTheme === 'dark') document.body.classList.add('dark');

  document.querySelectorAll('.theme-toggle').forEach((button) => {
    button.addEventListener('click', () => {
      document.body.classList.toggle('dark');
      localStorage.setItem('lexportal_theme', document.body.classList.contains('dark') ? 'dark' : 'light');
    });
  });

  document.querySelectorAll('.nav-toggle').forEach((button) => {
    button.addEventListener('click', () => document.querySelector('.nav-main')?.classList.toggle('mobile-open'));
  });

  document.querySelectorAll('.has-dropdown > .nav-link').forEach((link) => {
    link.addEventListener('click', (event) => {
      if (window.innerWidth > 900) event.preventDefault();
      link.parentElement.classList.toggle('open');
    });
  });

  document.querySelectorAll('[data-open-modal]').forEach((button) => {
    button.addEventListener('click', () => document.getElementById(button.dataset.openModal)?.classList.add('show'));
  });

  document.querySelectorAll('[data-close-modal]').forEach((button) => {
    button.addEventListener('click', () => button.closest('.modal-overlay')?.classList.remove('show'));
  });

  document.querySelectorAll('[data-swap-modal]').forEach((link) => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      document.querySelectorAll('.modal-overlay').forEach((modal) => modal.classList.remove('show'));
      document.getElementById(link.dataset.swapModal)?.classList.add('show');
    });
  });

  document.querySelectorAll('.modal-overlay').forEach((overlay) => {
    overlay.addEventListener('click', (event) => {
      if (event.target === overlay) overlay.classList.remove('show');
    });
  });

  document.querySelectorAll('.chip, .qs-tab, .role-option').forEach((chip) => {
    chip.addEventListener('click', () => {
      if (chip.classList.contains('qs-tab')) {
        chip.parentElement.querySelectorAll('.qs-tab').forEach((item) => item.classList.remove('active'));
      }
      if (chip.classList.contains('role-option')) {
        chip.parentElement.querySelectorAll('.role-option').forEach((item) => item.classList.remove('active'));
        chip.querySelector('input')?.click();
      }
      chip.classList.toggle('active');
    });
  });

  document.querySelectorAll('[data-voice-search]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      if (!Recognition) {
        alert("Voice search isn't supported in this browser. Try Chrome or Edge.");
        return;
      }
      const input = document.querySelector(trigger.dataset.voiceSearch);
      const recognition = new Recognition();
      recognition.lang = 'en-UG';
      recognition.onresult = (event) => {
        if (input) input.value = Array.from(event.results).map((result) => result[0].transcript).join('');
      };
      recognition.start();
    });
  });
})();
