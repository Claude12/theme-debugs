/**
 * Helper function to perform action on target elements if source element has particular single or set of inline style rules.
 *  Function accepts cb function that gets run if condition is metorif itnot met. This function receives payload:
 *
 *  Receives data object:
 *   {
 *      src: DOM element  - where we check existing style attributes
 *      targets: Array - array of elements affected if conditions are met
 *      existingProps: Array - list of inline styles present on element
 *      propsToHave: Array - list of element to be present ( single element or every element ) on src
 *      conditionMet : Boolean - true if single or every style is present ( depends on exactmatch Flag )
 *      exactMatch: Boolean - set in props.if true - every propsToHave item met condition else at least one met.
 *   }
 *  
 *  configuration for the actual function:
 * const props = {
      source: '.ms-content', // checking style attribute against this. DEFAULT - .ms-content
      targets: ['.ms-content blockquote','.ms-content div'], // all instances of all elements within src will be affected DEFAULT - ['.ms-content blockquote']
      styleProps: ['color','fill','font-size'], // desired props to have on source DEFAULT - ['color']
      matchAll: false, // enable this if all props in styleProps should be present on source. Default false ( at least one of those in styleProps should be on source) DEFAULT - false
      matchClass: 'custom-class', // class name used on targets if autoAddClasses is set to true DEFAULT - 'style-match'
      autoAddClasses: true // Boolean - should we add classes automatically if condition is met. DEFAULT - true
    }

    This helper function will run only if source element is found. example usage:
    computedStyleAction(props , callback);
    computedStyleAction(props);
 */

function computedStyleAction(config, cb) {
  const props = config || {};
  const source = props.source || '.ms-content';
  const targetSel = props.targets || ['.ms-content blockquote'];
  const styleProps = props.styleProps || ['color'];
  const targetClass = props.matchClass || 'style-match';
  const matchAll = 'matchAll' in props ? props.matchAll : false;
  const autoAddClasses = 'autoAddClasses' in props ? props.autoAddClasses : true;
  const callback = cb || null;

  const sourceElems = Array.prototype.slice.call(document.querySelectorAll(source));
  if (sourceElems.length == 0) return;

  sourceElems.forEach(function (source) {
    if (shouldAssignClass(source) && autoAddClasses) addClasses(source);
  });

  function shouldAssignClass(source) {
    let assignClass = false;

    // Return if there are no custom inline styles
    if (source.style.length == 0) return false;

    let existingProps = [];
    for (let i = 0; i < source.style.length; i++) existingProps.push(source.style[i].trim());

    // All properties inside styleProps should be present inside elements style attr
    if (matchAll)
      assignClass = styleProps.every(function (prop) {
        return existingProps.indexOf(prop) !== -1;
      });

    // One of elements inside styleProps should be present within elements style attr
    if (!matchAll) {
      styleProps.forEach(function (prop) {
        if (existingProps.indexOf(prop) !== -1) assignClass = true;
      });
    }

    // run callback if one is set
    const payload = {
      src: source,
      targets: targetSel,
      existingProps: existingProps,
      propsToHave: styleProps,
      conditionMet: assignClass,
      exactMatch: matchAll,
    };

    if (callback) callback(payload);

    return assignClass;
  }

  function addClasses(source) {
    targetSel.forEach(function (sel) {
      let elements = Array.prototype.slice.call(source.querySelectorAll(sel));
      elements.forEach(function (element) {
        element.classList.add(targetClass);
      });
    });
  }
}
