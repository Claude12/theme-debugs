/* 
  Content blocks require following CSS
  height: 0;
  overflow: hidden;
  transition:ease 0.2s height; // alter transition speed as per needs.
*/

const KetchupAccordion = function(opts, callback) {
  if (!(this instanceof KetchupAccordion)) return new KetchupAccordion(opts, callback);

  const defaults = {
    selectors: {
      trigger: '.faq-trigger', //triggers selector
      content: '.faq-answer' // content selectors
    },
    classes: {
      triggerActive: 'expanded', // active class for trigger
      contentActive: 'expanded' // active class for content
    },
    config: {
      debounceTime: 20, // time in ms after which reset elements. (wait under user finishes resize)
      addClasses: true, // true - adding classes, false not adding.
      initialActive: null, // null for none or index value of initial item
      offClick: false // resets accordian if clicked anywhere but triggers and content
    }
  };

  const userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.init();
  return this;
};

KetchupAccordion.prototype.init = function() {
  const self = this;
  const selectors = this.props.selectors;
  const initialActive = this.props.config.hasOwnProperty('initialActive') ? this.props.config.initialActive : null;
  const debounceTime = this.props.config.debounceTime;
  this.triggers = Array.prototype.slice.call(document.querySelectorAll(selectors.trigger));
  this.contents = Array.prototype.slice.call(document.querySelectorAll(selectors.content));
  this.boundToggle = this.toggleItem.bind(this);
  this.initTriggers();

  // Reset items on resize;
  window.addEventListener('resize', self.debounceFunc.call(self, self.reset, debounceTime, false), false);

  // Set initially active FAQ
  if (initialActive !== null) this.setActive(initialActive);

  // Initialize off click if this is needed
  if (this.props.config.offClick === true) this.initOffclick();

  return this;
};

KetchupAccordion.prototype.debounceFunc = function(func, wait, immediate) {
  const self = this;
  var timeout;
  return function() {
    var context = self,
      args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};

KetchupAccordion.prototype.initOffclick = function() {
  const self = this;
  const props = this.props;
  const initialActive = this.props.config.initialActive || null;

  document.addEventListener('click', function(event) {
    let clickedTrigger = false;
    let clickedContent = false;

    self.triggers.map(function(trigger) {
      if (trigger.contains(event.target)) {
        clickedTrigger = true;
      }
    });

    self.contents.map(function(trigger) {
      if (trigger.contains(event.target)) {
        clickedContent = true;
      }
    });

    if (!clickedTrigger && !clickedContent) {
      self.reset();
      if (initialActive !== null) self.setActive(initialActive);
    }
  });
};

KetchupAccordion.prototype.setActive = function(index) {
  const self = this;
  const classProps = this.props.classes;
  if (index > self.triggers.length || index > self.contents.length) return this;
  if (this.props.config.addClasses === true) {
    this.switchClass(self.triggers, index, classProps.triggerActive);
    this.switchClass(self.contents, index, classProps.contentActive);
  }
  this.toggleHeight(index);
};

KetchupAccordion.prototype.initTriggers = function() {
  const self = this;
  self.triggers.forEach(function(trigger) {
    trigger.addEventListener('click', self.boundToggle, false);
  });
  return this;
};

KetchupAccordion.prototype.destroyTriggers = function() {
  const self = this;
  self.triggers.forEach(function(trigger) {
    trigger.removeEventListener('click', self.boundToggle, false);
  });
  return this;
};

KetchupAccordion.prototype.toggleItem = function(e) {
  const activeIndex = this.triggers.indexOf(e.currentTarget);
  const activeContent = this.contents[activeIndex];
  const alreadyActive = activeContent.hasAttribute('style') && parseFloat(activeContent.style.height) > 1;

  if (this.props.config.addClasses === true) {
    this.switchClass(this.triggers, activeIndex, this.props.classes.triggerActive);
    this.switchClass(this.contents, activeIndex, this.props.classes.contentActive);
  }

  if (alreadyActive) {
    this.contents[activeIndex].style.height = '0px';
  } else {
    this.toggleHeight(activeIndex);
  }
  return this;
};

KetchupAccordion.prototype.reset = function() {
  const self = this;
  const props = self.props.classes;
  self.contents.map(function(content) {
    content.classList.remove(props.contentActive);
    content.removeAttribute('style');
  });

  self.triggers.map(function(trigger) {
    trigger.classList.remove(props.triggerActive);
  });

  // Set initially active FAQ
  if (this.props.config.initialActive !== null) this.setActive(this.props.config.initialActive);

  return this;
};

KetchupAccordion.prototype.toggleHeight = function(index) {
  const targetContent = this.contents[index];
  const contentHeight = targetContent.scrollHeight;

  this.contents.map(function(content) {
    if (content.hasAttribute('style')) {
      content.style.height = '0' + 'px';
    }
    targetContent.style.height = contentHeight + 'px';
    return this;
  });
};

KetchupAccordion.prototype.switchClass = function(elems, itemIndex, activeClass) {
  elems.forEach(function(item, index) {
    if (item.classList.contains(activeClass)) {
      item.classList.remove(activeClass);
    } else {
      item.classList.remove(activeClass);
      if (itemIndex === index) item.classList.add(activeClass);
    }
  });
  return this;
};

KetchupAccordion.prototype.destroy = function() {
  const self = this;
  self.reset();
  self.destroyTriggers();
  return this;
};

KetchupAccordion.prototype.mergeObj = function() {
  // Variables
  const self = this;
  var extended = {};
  var deep = false;
  var i = 0;
  var length = arguments.length;

  // Check if a deep merge
  if (Object.prototype.toString.call(arguments[0]) === '[object Boolean]') {
    deep = arguments[0];
    i++;
  }

  // Merge the object into the extended object
  var merge = function(obj) {
    for (var prop in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, prop)) {
        // If deep merge and property is an object, merge properties
        if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
          extended[prop] = self.mergeObj(true, extended[prop], obj[prop]);
        } else {
          extended[prop] = obj[prop];
        }
      }
    }
  };

  // Loop through each object and conduct a merge
  for (i = 0; i < length; i++) {
    var obj = arguments[i];
    merge(obj);
  }

  return extended;
};

/*
demoAccordion.setActive(index); // set active element (trigger and content) by its index
demoAccordion.destroyTriggers(); // removes events listener from triggers
demoAccordion.destroy();// removes classes, css and removes click event
demoAccordion.toggleHeight(index); // expands corresponding content block if it exists (index - index val of content block)
demoAccordion.reset(); // removes css and class names. keeps click event

*/
