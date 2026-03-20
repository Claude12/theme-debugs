/**
 *  Helper function to add class name depending on data attribute of any source.
 *  Useful for lements you dont have direct access or that are created dynamically
 */
function detectProp(sel, target, attr, cb) {
  const elements = Array.prototype.slice.call(document.querySelectorAll(sel));

  elements.forEach(function (element) {
    let source = element.querySelector(target);
    if (source && source.hasAttribute(attr)) {
      let result = source.getAttribute(attr);
      if (cb) cb(element, result);
    }
  });
}
