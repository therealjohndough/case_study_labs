(function(){
  /**
   * Mobile navigation toggle
   */
  const body = document.body;
  const hamburger = document.querySelector('.hamburger-menu');
  const nav = document.querySelector('.main-navigation');
  if (hamburger && nav) {
    hamburger.addEventListener('click', function(){
      const isOpen = body.classList.toggle('nav-open');
      this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      this.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
    });
  }

  /**
   * Gallery slider (simple fade) with accessibility enhancements
   * Attach to elements with class .js-simple-slider
   */
  const sliders = document.querySelectorAll('.js-simple-slider');
  sliders.forEach(function(slider){
    const slides = slider.querySelectorAll('.js-slide');
    const prev = slider.querySelector('.js-prev');
    const next = slider.querySelector('.js-next');
    const status = slider.querySelector('.js-status');
    let current = 0;

    function update() {
      slides.forEach((s, i) => {
        s.style.display = i === current ? 'block' : 'none';
      });
      if (status) status.textContent = (current + 1) + ' / ' + slides.length;
    }

    if (prev) prev.addEventListener('click', () => {
      current = (current - 1 + slides.length) % slides.length;
      update();
    });
    if (next) next.addEventListener('click', () => {
      current = (current + 1) % slides.length;
      update();
    });

    slider.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') { e.preventDefault(); prev.click(); }
      if (e.key === 'ArrowRight') { e.preventDefault(); next.click(); }
    });

    update();
  });
})();
