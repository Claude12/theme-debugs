/**
 *  Helper function to allow custom colours for custom bullets.
 *  functionality relies on slug ( km-bullet-slug ) on parent. this should contain slug value from KM picker
 *  to use function : call it with two arguments. 1. array of your modules. 2 - slug name
 *  Example: adjustBullets(['km-generic-block', 'column-content'], 'km-bullet-slug');
 */
function adjustBullets(src, attribute) {
  if (!src || src.length === 0) return;

  src.forEach(function (section) {
    let blocks = Array.prototype.slice.call(
      document.getElementsByClassName(section)
    );

    blocks.forEach(function (block) {
      if (block.hasAttribute(attribute)) {
        let slug = block.getAttribute(attribute);
        addSlug(block, '.kcb-icon', ['has-' + slug + '-fill']);
        addSlug(block, '.kcb-number', ['has-' + slug + '-colour']);
        addSlug(block, '.kcb-spacer', ['has-' + slug + '-background-colour']);
      }
    });
  });

  function addSlug(parent, sel, classNames) {
    let items = Array.prototype.slice.call(parent.querySelectorAll(sel));
    items.forEach(function (item) {
      classNames.forEach(function (className) {
        item.classList.add(className);
      });
    });
  }
}
