/**
 * Allows adding class name to dynamic child of any selector
 *  assignClass(['.dark ul','.dark ol'], 'has-dark-bg');
 *  assignClass([array of selectors], desired-class-name);
 *  Use case : Bullets for example. Sometimes they are on dark, sometimes on light background. And they are dynamic from WYISWYG.
 *  This function will help to control these bullets.
 **/

function assignClass(arr, className) {
  const source = arr || [];
  const targetClass = className || 'added-class';

  source.forEach(function (item) {
    let set = Array.prototype.slice.call(document.querySelectorAll(item));
    updateSelection(set);
  });

  function updateSelection(arr) {
    arr.forEach(function (list) {
      list.classList.add(targetClass);
    });
  }
}

// Example:
// window.addEventListener('DOMContentLoaded', function() {
//   assignClass(['.has-dark-bg ul', '.has-light-bg ol'], 'has-dark-bg');
//   assignClass(['.has-light-bg ul', '.has-light-bg ol'], 'has-light-bg');
// });

/**
 *  Sample PHP - ACF gives boolean 
    // $blockClasses = ['split-content-section'];
    // $bgType = is_array(get_field('bullet_type')) ? get_field('bullet_type')['result'] : false;
    // $bgClass = $bgType === true ? 'km-has-dark-bg' : 'km-has-light-bg';
    // array_push($blockClasses, $bgClass);
 *   <?php echo implode(' ', $blockClasses); ?>
 */
