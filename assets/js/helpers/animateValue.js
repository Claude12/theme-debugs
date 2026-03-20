/**
 * 
 * @param {DOM ELement} obj DOM element to animate
 * @param {Integer} start Starting point for animation
 * @param {Iteger} end End point of animation
 * @param {Integer} duration Animation duration
 */
function animateValue(obj, start, end, duration = 3000) {
  let startTimestamp = null;
  const step = (timestamp) => {
    if (!startTimestamp) startTimestamp = timestamp;
    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
    obj.innerHTML = Math.floor(progress * (end - start) + start);
    if (progress < 1) {
      window.requestAnimationFrame(step);
    }
  };
  window.requestAnimationFrame(step);
}

