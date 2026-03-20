const KetchupBuilder = function(opts, callback) {
  if (!(this instanceof KetchupBuilder)) return new KetchupVideoBg(opts, callback);

  const defaults = {
    targetMedia: '(min-width:981px)',
    responsive: true, // re calculated on resize!
    callbackArgs: null, // object or anything else to pass as extra argument to callback function (key - custom)
    buildElement: {
      parent: null, // apendTo element x
      tag: 'span', // tag for element
      classes: '', // any classes?
      eleH: 20, // in pixels including margins
      eleW: 20, // in pixels including margin
      buildAxis: 'x', // or y,
      identifierPrefix: null // replace with string if you want to mark your markers with prefix + number
    },
    elements: []
  };
  const userData = opts || {};
  this.data = {
    builtElements: [],
    eleA: null,
    eleB: null,
    centerDistance: null
  };
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.mediaCheck = window.matchMedia(this.props.targetMedia);
  this.mediaMatches = false;
  this.init();
  return this;
};

KetchupBuilder.prototype.init = function() {
  const self = this;
  self.boundRebuild = self.rebuild.bind(self);

  self.data.parent = document.querySelector(self.props.buildElement.parent);
  // Do nothing if there are not two elements
  const elems = this.props.elements;
  if (elems.length !== 2) return;
  this.data.eleA = document.querySelector(elems[0]);
  this.data.eleB = document.querySelector(elems[1]);
  if (!this.data.eleA || !this.data.eleB) return;
  self.data.centerDistance = self.getDistance(self.data.eleA, self.data.eleB);

  // Check Media
  this.mediaCheck.addListener(self.detectView.bind(self));
  this.detectView.call(self, self.mediaCheck);
};

KetchupBuilder.prototype.buildElements = function() {
  const self = this;
  const parent = self.data.parent;
  const props = this.props.buildElement;
  const numericPrefix = props.identifierPrefix;
  if (!self.data.parent) throw new Error('No Parent found. Unable to build elements (props.buildElement.parent)');
  const buildAxis = props.buildAxis;
  const parentCenter = props.buildAxis === 'x' ? parent.getBoundingClientRect().width / 2 : parent.getBoundingClientRect().height / 2;
  const eleStep = buildAxis === 'x' ? props.eleW : props.eleH;
  const eleAmount = parseInt(self.data.centerDistance / eleStep);
  let currStep = parentCenter;

  for (let x = 0; x <= eleAmount; x++) {
    let ele = document.createElement(props.tag);
    let identifierClass = numericPrefix !== null ? ' ' + numericPrefix + (x + 1) : '';
    if (props.classes.trim() !== '') ele.className = props.classes + identifierClass;
    buildAxis === 'x' ? (ele.style.left = currStep + 'px') : (ele.style.top = currStep + 'px');

    this.data.builtElements.push(ele);
    parent.appendChild(ele);
    currStep += eleStep;
  }
};

KetchupBuilder.prototype.removeElements = function() {
  const self = this;
  const elems = self.data.builtElements;
  elems.forEach(function(elem) {
    elem.parentNode.removeChild(elem);
  });
  self.data.builtElements = [];
  return this;
};

KetchupBuilder.prototype.getCenter = function(element) {
  const top = element.getBoundingClientRect().top;
  const left = element.getBoundingClientRect().left;
  const width = element.getBoundingClientRect().width;
  const height = element.getBoundingClientRect().height;
  return {
    x: left + width / 2,
    y: top + height / 2
  };
};

KetchupBuilder.prototype.getDistance = function(a, b) {
  const aPosition = this.getCenter(a);
  const bPosition = this.getCenter(b);
  return Math.sqrt(Math.pow(aPosition.x - bPosition.x, 2) + Math.pow(aPosition.y - bPosition.y, 2));
};

KetchupBuilder.prototype.detectView = function(query) {
  const self = this;
  const action = query.matches ? 'build' : 'destroyed';

  if (query.matches) {
    this.mediaMatches = true;
    this.buildElements();
    if (self.props.responsive === true) window.addEventListener('resize', self.boundRebuild, false);
  } else {
    this.mediaMatches = false;
    if (self.props.responsive === true) window.removeEventListener('resize', self.boundRebuild, false);
    this.removeElements();
  }

  self.servePayload(action);
  return this;
};

KetchupBuilder.prototype.servePayload = function(action) {
  const self = this;
  const payload = {
    data: self.data,
    action: action,
    axis: self.props.buildElement.buildAxis,
    custom: self.props.callbackArgs
  };
  if (self.callback) self.callback(payload);
};

KetchupBuilder.prototype.rebuild = function() {
  const self = this;
  self.data.centerDistance = self.getDistance(self.data.eleA, self.data.eleB);
  self.removeElements();
  self.buildElements();
  self.servePayload('rebuild');
};

KetchupBuilder.prototype.mergeObj = function() {
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
