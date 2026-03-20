/**
 * Function to create tabbed content from trigger and content
 * Usage:
 * kmTabs({
 *    initial: 1, // Initially active tab. Should match data attribute value DEFAULT: null
 *    activeClass: 'active', // Active class for active tab DEFAULT: active
 *    triggers: Array.prototype.slice.call(document.querySelectorAll('.tablinks')), // array of triggers. DOM elems DEFAULT: []
 *    contents: Array.prototype.slice.call(document.querySelectorAll('.tabcontent')), // array of contents. DOM elems DEFAULT: []
 *    attr: 'data-km-tab', // attribute for match DEFAULT: data-km-tab
 *    addContentAttr: true, // inject attribute via JS? // useful for inaccessible elems DEFAULT: false
 *    addTriggerAttr: true, // inject attribute via JS? // useful for inaccessible elems DEFAULT: false
 *  });
 *
 **/
function kmTabs(config) {
  const props = config || {};
  const initial = props.initial || null;
  const activeClass = props.active || 'active';
  const triggers = props.triggers || [];
  const contents = props.contents || [];
  const attr = props.attr || 'data-km-tab';
  const addAttrTriggers = props.addTriggerAttr === true ? true : false;
  const addAttrContents = props.addContentAttr === true ? true : false;

  if (addAttrContents) {
    contents.forEach((content, index) => {
      addAttribute(content, index + 1);
    });
  }

  triggers.forEach((trigger, index) => {
    if (addAttrTriggers) addAttribute(trigger, index + 1);

    trigger.addEventListener(
      'click',
      (e) => {
        e.preventDefault();
        let target = parseInt(e.currentTarget.getAttribute(attr));
        switchTabs(triggers, target);
        switchTabs(contents, target);
      },
      false
    );
  });

  function switchTabs(set, target) {
    set.forEach((item) => {
      let tabId = parseInt(item.getAttribute(attr));
      let action = tabId !== target ? 'remove' : 'add';
      item.classList[action](activeClass);
    });
  }

  function addAttribute(target, value) {
    target.setAttribute(attr, value);
  }

  if (initial) {
    switchTabs(triggers, initial);
    switchTabs(contents, initial);
  }
}

kmTabs({
  initial: 1, // Initially active tab. Should match data attribute value DEFAULT: null
  triggers: Array.prototype.slice.call(document.querySelectorAll('.tablinks')), // array of triggers. DOM elems DEFAULT: []
  contents: Array.prototype.slice.call(
    document.querySelectorAll('.tabcontent')
  ), // array of contents. DOM elems DEFAULT: []
  addContentAttr: true, // inject attribute via JS? // useful for inaccessible elems DEFAULT: false
  addTriggerAttr: true, // inject attribute via JS? // useful for inaccessible elems DEFAULT: false
});
