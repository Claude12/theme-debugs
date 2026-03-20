(function scrollToTop() {
  const btn = document.getElementById('scroll-to-top');
  if (!btn) return;

  const bntActive = 'stt-visible';
  const scrollDuration = btn.dataset.speed || 400;
  const revealDistance = btn.dataset.reveal || 10; // 10px from the top

  btn.addEventListener('click', moveToTop, false);

  // Check if should be visible
  const checkVisibility = function () {
    window.pageYOffset >= revealDistance
      ? btn.classList.add(bntActive)
      : btn.classList.remove(bntActive);
  };

  checkVisibility();
  window.addEventListener('scroll', checkVisibility);

  // Move to top
  function moveToTop() {
    let cosParameter = window.pageYOffset / 2;
    let scrollCount = 0;
    let oldTimestamp = performance.now();

    function step(newTimestamp) {
      scrollCount += Math.PI / (scrollDuration / (newTimestamp - oldTimestamp));
      if (scrollCount >= Math.PI) window.scrollTo(0, 0);

      if (parseInt(window.pageYOffset) === 0) return;
      window.scrollTo(
        0,
        Math.round(cosParameter + cosParameter * Math.cos(scrollCount))
      );

      oldTimestamp = newTimestamp;
      window.requestAnimationFrame(step);
    }

    window.requestAnimationFrame(step);
  }
})();
