/**
 *
 * Function to add particular class to element after X pixels of scroll.
 * scrollClass({
 *  element: document.querySelector('.top-bar'), //mandatory
 *  class: 'change-bg', // optional DEFAULT - active
 *  addAfter: 200, // optional DEFAULT - 100
 * }callback);
 *  2nd arg - callback. receives payload as first arg.
 */
function scrollClass(config, cb) {
  const props = config || {};
  const element = props.element || document.querySelector('.top-bar');
  const isAdmin = document.body.classList.contains('wp-admin');
  const dist = props.addAfter || 100;
  const className = props.class || 'active';

  if (!element || isAdmin) return;

  const payload = {
    element: element,
    dist: dist,
    class: className,
    action: null,
  };

  let state;

  const checkVisibility = function () {
    if (window.pageYOffset >= dist) {
      element.classList.add(className);
      runCb('add');
      state = 'add';
    } else {
      element.classList.remove(className);
      runCb('remove');
      state = 'remove';
    }
  };

  function runCb(currAction) {
    if (currAction === state) return;
    if (cb) {
      payload.action = window.pageYOffset >= dist ? 'add' : 'remove';
      cb(payload);
    }
  }
  checkVisibility();
  window.addEventListener('scroll', checkVisibility);
}
