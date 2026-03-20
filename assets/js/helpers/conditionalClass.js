/*
 * Function adds class name to all target elements if previous or next sibling * has class of X
 * Options:
 * const props = {
 *   selector: '.selector' // string of element that receives class  DEFAULT : '.check-sibling'
 *   type: 'previous' // check previous sibling with 'previous' and next with anything else . DEFAULT : 'previous'
 *   classCheck : 'class name' // class name to check on sibling // default - 'previous-sibling'
 *   classToAdd : 'class name' // this class name will be added to elements within selector match
 *   media: 'media query'// this functionality will be used within matching media DEFAULT : '(min-width: 981px)'
 *  }
 *  conditionalClass(props);
 */
function conditionalClass(opts) {
  const defaults = {
    selector: '.box-1',
    type: 'before',
    classCheck: '.class-to-check',
    classToAdd: 'padding-top',
    media: '(min-width: 981px)'
  };
  const userProps = opts || {};
  const props = { ...defaults, ...userProps };
  const mediaCondition = window.matchMedia(props.media);
  const checkMedia = mediaCondition => (mediaCondition.matches ? action() : destroy());
  const elements = Array.prototype.slice.call(document.querySelectorAll(props.selector));
  if (elements.length === 0) return;
  mediaCondition.addListener(checkMedia);
  checkMedia(mediaCondition);

  function action() {
    elements.forEach(item => {
      let targetSibling = props.type === 'previous' ? item.previousElementSibling : item.nextElementSibling;
      if (!targetSibling) return;
      if (targetSibling.classList.contains(props.classCheck)) item.classList.add(props.classToAdd);
    });
  }

  function destroy() {
    elements.forEach(item => item.classList.remove(props.classToAdd));
  }
}
