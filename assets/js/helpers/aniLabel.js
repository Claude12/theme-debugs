/**
 *  Function to add class name to active input fields label on Ninja forms (only!)
 *  jQuery(document).on('nfFormReady', function () {
 *     aniLabel();
 *   });
 */
function aniLabel() {
  const inputs = Array.prototype.slice.call(
    document.querySelectorAll('.ninja-forms-field.nf-element')
  );

  inputs.forEach((input) => {
    input.addEventListener(
      'focus',
      () => {
        labelAction(input, true);
      },
      false
    );

    input.addEventListener(
      'blur',
      () => {
        labelAction(input, false);
      },
      false
    );
  });
}

function labelAction(input, isActive) {
  let target = input.closest('.nf-field');
  const lbl = target.querySelector('.nf-field-label');
  const action = isActive ? 'add' : 'remove';
  const activeClass = 'ninja-lbl-active';

  if (lbl) {
    if (input.value.trim() === '') {
      lbl.classList[action](activeClass);
    } else {
      lbl.classList.add(activeClass);
    }
  }
}