const KetchupModal = function(opts, callback) {
  if (!(this instanceof KetchupModal)) return new KetchupModal(opts, callback);

  const defaults = {
    selectors: {
      modal: '.km-modal-wrap',
      trigger: '.km-modal-trigger'
    },
    classes: {
      modalActive: 'km-active' || null,
      triggerActive: 'km-active' || null,
      bodyClass: 'km-active' || null
    },
    offClick: {
      enabled: true,
      element: '.km-modal-content' //null // if null it will check agains selectors.modal and triggers
    }
  };
  const userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.init();
  return this;
};

KetchupModal.prototype.init = function() {
  const self = this;
  const selectors = this.props.selectors;
  this.triggers = Array.prototype.slice.call(document.querySelectorAll(selectors.trigger));
  const contentBlock = document.querySelector(selectors.modal);
  if (!contentBlock) return;
  this.contentBlock = contentBlock;
  this.boundToggleModal = this.toggleModal.bind(self);

  this.triggers.map(function(trigger) {
    trigger.addEventListener('click', self.boundToggleModal, false);
  });

  // Initialize off click if this is needed
  if (this.props.offClick.enabled === true) this.initOffclick();
};

KetchupModal.prototype.initOffclick = function() {
  const self = this;
  const props = this.props;
  const target = props.offClick.element === null ? this.contentBlock : document.querySelector(props.offClick.element);

  document.addEventListener('click', function(event) {
    let clickedTrigger = false;
    self.triggers.map(function(trigger) {
      if (trigger.contains(event.target)) {
        clickedTrigger = true;
      }
    });

    if (!target.contains(event.target) && clickedTrigger === false) {
      self.reset();
      self.servePayload('off-click');
    }
  });
};

KetchupModal.prototype.destroy = function() {
  const self = this;
  // Remove events listener;
  this.triggers.map(function(trigger) {
    trigger.removeEventListener('click', self.boundToggleModal, false);
  });
  this.reset();
};

KetchupModal.prototype.reset = function() {
  const classProps = this.props.classes;
  const modal = this.contentBlock;

  if (classProps.modalActive !== null) modal.classList.remove(classProps.modalActive);
  if (classProps.bodyClass !== null) document.body.classList.remove(classProps.bodyClass);

  if (classProps.triggerActive !== null) {
    this.triggers.map(function(trigger) {
      trigger.classList.remove(classProps.triggerActive);
    });
  }
};

KetchupModal.prototype.toggleModal = function() {
  const classProps = this.props.classes;
  const modal = this.contentBlock;

  if (!modal) return;
  if (classProps.modalActive !== null) modal.classList.toggle(classProps.modalActive);
  if (classProps.bodyClass !== null) document.body.classList.toggle(classProps.bodyClass);

  // Add active class to trigger if one is set
  if (classProps.triggerActive !== null) {
    this.triggers.map(function(trigger) {
      trigger.classList.toggle(classProps.triggerActive);
    });
  }
  this.servePayload('button');
};

KetchupModal.prototype.servePayload = function(source) {
  if (!this.callback) return;
  const payload = {
    triggers: this.triggers,
    modal: this.contentBlock,
    classes: this.props.classes,
    modalState: this.contentBlock.classList.contains(this.props.classes.modalActive) ? 'open' : 'closed',
    offClickEnabled: this.props.offClick.enabled
  };

  if (source) payload.source = source;
  this.callback(payload);
};

KetchupModal.prototype.mergeObj = function() {
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
