/**
 * Checks if user clicked on any element matching selector in first argument.
 * If clicked outside - it executes function ( second argument ) giving array of elements as argument.
 * Callback function will recieve 2d array ([[],[],[]]) if 1 argument is array and 1d array ([]) if 1 argument is string.
 * sel: array of selectors or single selector string - required
 * cb: function to execute if user clicked outside all elements in "sel" - required
 * example:
 *  clickedOutside(['.content','.trigger'], myFunc);
 *  clickedOutside('.content', myFunc);
 */
function clickedOutside(sel, cb) {
  if (!sel || !cb) throw new Error('Too few arguments given. Expected "array or string of selectors", "function"');
  if (typeof sel !== 'string' && !isArray(sel)) throw new Error('Argument 1 should be string or array');

  let eleArr = [];

  if (isArray(sel)) {
    sel.forEach(function(item) {
      let elements = Array.prototype.slice.call(document.querySelectorAll(item));
      if (elements.length > 0) eleArr.push(elements);
    });
  } else if (typeof sel === 'string') {
    let elements = Array.prototype.slice.call(document.querySelectorAll(sel));
    if (elements.length > 0) eleArr.push(elements);
  }
  const output = eleArr.length === 1 && !isArray(sel) ? eleArr[0] : eleArr;
  document.addEventListener('click', function(event) {
    let clicked = false;
    eleArr.forEach(function(elems) {
      elems.forEach(function(elem) {
        let containsTarget = elem.contains(event.target);
        if (containsTarget) clicked = true;
      });
    });
    if (!clicked) cb(output);
    return clicked;
  });

  function isArray(obj) {
    return !!obj && obj.constructor === Array;
  }
}

/*
 *** DEMO
 *** DEMO
 *** DEMO
 *** DEMO
 *** DEMO
 */

// Sample with multiple elements (useful when not sharing same parent)
//=====================
// elems will be 2d array because we pass array as 1 argument
// clickedOutside(['.box', '.trigger', '.demo'], myFunc);
// function myFunc(elems) {
//   console.log(elems);
//   //Remove class for all
//   elems.forEach(function(set) {
//     set.forEach(function(item) {
//       console.log(item.className);
//       item.classList.remove('active');
//     });
//   });
//   // print out elements you did not click within
//   console.log('You did NOT click within:');
//   elems.forEach(function(items) {
//     items.forEach(function(html) {
//       console.log(html);
//     });
//   });
// }

// Single Selector DEMO
//=====================
// items will be 1d array because we passed in string as 1 argument
// clickedOutside('.demo', cb);
// function cb(items){
//   console.log('You did NOT click within:')
//   items.forEach(function(html){
//     console.log(html);
//   });
// }
