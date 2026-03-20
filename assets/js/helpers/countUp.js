/*
 *   {
 *     selector: '.ss-item .ssi-number', // default 0
 *      incrementStep: 200, // default 100
 *      startFrom: 1, // default 0;
 *      datasetKey: 'total'// default total ( build data-total attribute )
 *    }
 */

function countUp(props) {
  const incStep = props.incrementStep || 100; // control speed with this;
  const startFrom = props.startFrom || 0;
  const itemsSel = props.selector || null;
  const dataHolder = props.datasetKey || 'total';
  const items = Array.prototype.slice.call(document.querySelectorAll(itemsSel));
  const regex = /[.,\s]/g;
  if (items.length === 0) return;

  items.forEach(function(item) {
    let current = startFrom;
    let total = parseInt(item.dataset[dataHolder].replace(regex, ''));
    let increment = Math.ceil(total / incStep);

    function step() {
      current += increment;
      if (current > total) current = total;
      item.textContent = current.toLocaleString();
      current !== total && requestAnimationFrame(step);
    }
    step();
  });
}
