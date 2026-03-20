/*
  DEPRECATED - Please use injectBullets instead
 * Function to replace default list bullets.
 * Will create span with class of list-bullet ( blank for ul's and numbered for ol's)
 * Optional arguments :
 *  1. Array . Set of selectors that should be excluded from attaching bespoke bullets. for example if argument is ['.ignore-my-uls'] - bespokeBullets will leave all uls and ols intact in this element. By default '.main-nav' and '.main-footer' is ignored autmatically and these items should not be passed within this argument.
 *  2. Boolean. true - existing content of li will be wrapped with span with class of list-content
 *
 */

function bespokeBullets(excludeList, wrapFlag) {
  const defaultSkips = ['.main-nav', '.main-footer']; // skip all ol's and ul's within these parents
  const skip = (excludeList && defaultSkips.concat(excludeList)) || defaultSkips; // user defined skips
  const lists = ['ol', 'ul']; // dl skipped as it is unique and should be handled separately
  const wrapExisting = typeof wrapFlag === 'boolean' ? wrapFlag : false; // wrap current content with HTML?
  const skipChecklist = []; // List of DOM elements to check relation with

  // Populate skip check Nodes
  skip.forEach(function (skipEle) {
    // let parent = document.querySelector(skipEle);
    // if (parent) skipChecklist.push(parent);

    let parents = Array.prototype.slice.call(document.querySelectorAll(skipEle));
    parents.forEach(function (parent) {
      skipChecklist.push(parent);
    });
  });

  // Perform action on all UL's or OL's on the page
  lists.forEach(function (listType) {
    let parents = Array.prototype.slice.call(document.querySelectorAll(listType));

    parents.forEach(function (parent) {
      // Skip list sets if set parent is a child of exclude array item
      let skipSet = false;
      skipChecklist.forEach(function (skip) {
        if (skip !== parent && skip.contains(parent)) skipSet = true;
      });

      // Skip attaching stuff.
      if (skipSet) return;

      let children = Array.prototype.slice.call(parent.querySelectorAll('li'));

      // Match found. Action required - create and attach bullet
      children.forEach(function (child, index) {
        // Wrap Existing content with a span
        if (wrapExisting) {
          let existingContent = document.createElement('span');
          existingContent.className = 'list-content';
          existingContent.innerHTML = child.innerHTML;

          while (child.firstChild) {
            child.removeChild(child.firstChild);
          }

          child.appendChild(existingContent);
        }

        // Append span with or without content
        let ele = document.createElement('span');
        ele.className = 'list-bullet';
        ele.innerHTML = listType === 'ul' ? '&nbsp;' : index + 1;
        child.insertBefore(ele, child.childNodes[0]);
      });
    });
  });
}

/* Sample Usage
const excludeList = ['.skip-all-uls-and-ols-in-me', '.another-skippabel-parent'];
bespokeBullets(excludeList,true); // skip all lists from exludeList and wrap existing content with span

bespokeBullets(null,true); // do not provide extra exludes but wrap existing content

bespokeBullets([],true); // same as above

Minimal typical usage

bespokeBullets();

*/
