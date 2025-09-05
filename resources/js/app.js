import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Lightweight reveal-on-scroll for top page elements
(() => {
  const items = document.querySelectorAll('[data-animate]');
  if (!items.length) return;

  const onIntersect = (entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  };

  // Skip animations for reduced motion
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduceMotion) {
    items.forEach(el => el.classList.add('is-visible'));
    return;
  }

  const io = new IntersectionObserver(onIntersect, { rootMargin: '0px 0px -10% 0px', threshold: 0.1 });
  items.forEach(el => io.observe(el));
})();
