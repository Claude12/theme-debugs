/**
 * Function to add slug for any target element
 *      addSlug(element, '.swiper-pagination-bullet svg', [
        'has-' + slug + '-fill',
 */
function addSlug(parent, sel, classNames) {
  let items = Array.prototype.slice.call(parent.querySelectorAll(sel));
  items.forEach(function (item) {
    classNames.forEach(function (className) {
      item.classList.add(className);
    });
  });
}
