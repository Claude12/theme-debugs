/*
 Options:
 {
  directionAttr: 'direction',
  setDir: true, // true/false,
  media: '(min-width: 981px)',
  sticky: '.sticky-element',
  stickyActive: 'active',
  dirUp: 'up',
  dirDown: 'down',
  dirLoad: 'load',
  topOffset: 100
}

 callback is optional
*/
const sticky = function (config, callback) {
  const props = config || {};
  const setDirection =
    props.setDir || props.setDir === false ? props.setDir : true;
  const stickyElement = props.sticky || '.sticky';
  const sticky = document.querySelector(stickyElement);

  if (!sticky) return;
  const topOffset = props.topOffset || 0;
  const media = props.media || '(min-width:981px)';
  const matchesMedia = window.matchMedia(media);
  const directionAttribute = props.directionAttr || 'direction';
  const supportOffset = window.pageYOffset !== undefined;
  const stickyActive = props.stickyActive || 'active';
  const dirUp = props.dirUp || 'up';
  const dirDown = props.dirUp || 'down';

  let stickyTop = sticky.getBoundingClientRect().top;
  let lastKnownPos = supportOffset
    ? window.pageYOffset
    : document.body.scrollTop;
  let ticking = 0;
  let scrollDir = props.dirLoad || 'load';
  let isSticky = 0;

  function action(lastKnownPos, scrollDir) {
    const stickyCheck = sticky.classList.contains(stickyActive) ? 1 : 0;
    stickyTop = sticky.getBoundingClientRect().top;

    if (stickyTop + topOffset <= lastKnownPos) {
      sticky.classList.add(stickyActive);
      isSticky = 1;
    } else {
      sticky.classList.remove(stickyActive);
      isSticky = 0;
    }
    let actionStatus = isSticky === 1 ? 'made-sticky' : 'removed-sticky';
    if (stickyCheck !== isSticky) runCallback(actionStatus);
    if (setDirection) sticky.dataset[directionAttribute] = scrollDir;
  }

  function measure() {
    let currYPos = supportOffset ? window.pageYOffset : document.body.scrollTop;
    scrollDir = lastKnownPos <= currYPos ? dirDown : dirUp;
    lastKnownPos = currYPos;

    if (!ticking) {
      window.requestAnimationFrame(function () {
        action(lastKnownPos, scrollDir);
        ticking = false;
      });
    }
    ticking = true;
  }

  function checkMedia() {
    if (matchesMedia.matches) {
      window.addEventListener('scroll', measure, false);
      if (setDirection) sticky.dataset[directionAttribute] = scrollDir;
    } else {
      window.removeEventListener('scroll', measure, false);
      reset();
    }
  }
  matchesMedia.addListener(checkMedia);
  checkMedia();

  function reset() {
    sticky.classList.remove(stickyActive);
    if (setDirection) sticky.removeAttribute('data-' + directionAttribute);
    runCallback('destroyed');
  }

  function runCallback(action) {
    if (!callback) return;
    const payload = {
      sticky: sticky,
      isSticky: sticky.classList.contains(stickyActive),
      stickyHeight: sticky.scrollHeight,
      stickyTop: stickyTop,
      direction: scrollDir,
      action: action,
    };
    callback(payload);
  }

  //measure();
  runCallback('page-load');
};
