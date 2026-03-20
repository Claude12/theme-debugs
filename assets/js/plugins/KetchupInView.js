// https://github.com/ketchupmarketing/KetchupInView

const KetchupInView = function (opts, callback) {
  if (!(this instanceof KetchupInView))
    return new KetchupInView(opts, callback);

  const defaults = {
    selector: null,
    partialOffsets: {
      // number of pixels in view for condition to be true
      top: 0,
      left: 0,
    },
    config: {
      fullyInView: true, // if true, callback will run only if element is fully in view. false, part of element should be visible on the screen. partialOffsets are used if particular amount of pixels should be visible.
    },
  };
  const userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.isInView = false;
  this.init();
  return this;
};

KetchupInView.prototype.init = function () {
  const self = this;
  this.boundCheckViewState = this.checkViewState.bind(this);
  const ele = this.props.selector;
  this.element = this.isEle(ele)
    ? ele
    : document.querySelector(this.props.selector);

  if (!this.element) return;

  window.addEventListener('scroll', self.boundCheckViewState, false);
  this.checkViewState();

  return this;
};

KetchupInView.prototype.isEle = function (element) {
  return element instanceof Element || element instanceof HTMLDocument;
};

KetchupInView.prototype.destroy = function () {
  const self = this;
  window.removeEventListener('scroll', self.boundCheckViewState, false);
  self.servePayload('destroyed');
  return this;
};

KetchupInView.prototype.checkViewState = function () {
  const self = this;
  const ele = this.element;
  const fullyVisible = this.props.config.fullyInView;
  const result =
    fullyVisible === true ? self.fullyInView(ele) : self.partiallyInView(ele);

  if (self.isInView !== result) {
    self.isInView = result;
    self.servePayload();
  }
  return this;
};

KetchupInView.prototype.partiallyInView = function (el) {
  const self = this;
  const rect = el.getBoundingClientRect();
  // DOMRect { x: 8, y: 8, width: 100, height: 100, top: 8, right: 108, bottom: 108, left: 8 }
  const windowHeight =
    window.innerHeight || document.documentElement.clientHeight;
  const windowWidth = window.innerWidth || document.documentElement.clientWidth;

  // http://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap
  const vertInView =
    rect.top + self.props.partialOffsets.top <= windowHeight &&
    rect.top + rect.height >= 0;
  const horInView =
    rect.left + self.props.partialOffsets.left <= windowWidth &&
    rect.left + rect.width >= 0;

  return vertInView && horInView;
};

KetchupInView.prototype.fullyInView = function (el) {
  const rect = el.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <=
      (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
};

KetchupInView.prototype.servePayload = function (source) {
  const payload = {
    element: this.element,
    isInView: this.isInView,
    source: 'state-change',
  };
  if (source) payload.source = source;
  if (this.callback) this.callback(payload);
  return this;
};

KetchupInView.prototype.mergeObj = function () {
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
  var merge = function (obj) {
    for (var prop in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, prop)) {
        // If deep merge and property is an object, merge properties
        if (
          deep &&
          Object.prototype.toString.call(obj[prop]) === '[object Object]'
        ) {
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
