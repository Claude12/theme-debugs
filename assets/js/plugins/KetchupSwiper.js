const KetchupSwiper = function(opts, callback) {
  if (!(this instanceof KetchupSwiper)) return new KetchupSwiper(opts, callback);

  const defaults = {
    enableMedia: '(max-width: 980px)',
    targetSwiper: '.conditional-swiper',
    matchBoth: false,
    classPrefix: null
  };

  const userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.currSwiper = null;
  this.swiperProps = {};
  this.count = Array.prototype.slice.call(document.querySelectorAll(this.props.targetSwiper + ' .swiper-slide'));
  this.cap = this.props.cap || this.count.length + 1;
  this.swiperMedia = window.matchMedia(this.props.enableMedia);
  this.classHolder = this.props.classHolder ? document.querySelector(this.props.classHolder) : null;
  this.swiperEnabled = false;

  // Add defaults to swiper props
  const swiperSettingsDefaults = {
    slideClass: 'swiper-slide',
    a11y: {
      enabled: false
    },
    // Below workaround for incorrect width for the slide on refresh.
    on: {
      init: function() {
        setTimeout(function() {
          if (typeof Event === 'function') {
            // modern browsers
            window.dispatchEvent(new Event('resize'));
          } else {
            // for IE and other old browsers
            // causes deprecation warning on modern browsers
            var evt = window.document.createEvent('UIEvents');
            evt.initUIEvent('resize', true, false, window, 0);
            window.dispatchEvent(evt);
          }
        }, 100);
      }
    }
  };

  this.swiperProps = this.mergeObj(swiperSettingsDefaults, this.props.swiperProps);

  return this;
};

KetchupSwiper.prototype.init = function() {
  const self = this;
  // Detect current view and listen to changes
  this.swiperMedia.addListener(this.swiperAction.bind(self));
  this.swiperAction.call(self, this.swiperMedia);

  return this;
};

KetchupSwiper.prototype.swiperAction = function(swiperMedia) {
  const condition = this.props.matchBoth === true ? swiperMedia.matches && this.cap <= this.count.length : swiperMedia.matches || this.cap <= this.count.length;

  if (condition && this.count.length > 1) {
    this.currSwiper = new Swiper(this.props.targetSwiper, this.swiperProps);

    if (this.classHolder) this.switchClass('on', 'off');
    this.swiperEnabled = true;
  } else {
    this.destroy();
    if (this.classHolder) this.switchClass('off', 'on');
    this.swiperEnabled = false;
  }
  if (this.callback) this.callback(this.payload());
};

KetchupSwiper.prototype.switchClass = function(add, remove) {
  const classPrefix = this.props.classPrefix === null ? '' : this.props.classPrefix;
  this.classHolder.classList.remove(classPrefix + remove);
  this.classHolder.classList.add(classPrefix + add);
  return this;
};

KetchupSwiper.prototype.payload = function() {
  const callbackData = {
    instance: this.props.targetSwiper,
    cap: this.cap,
    media: this.props.enableMedia,
    contentBlocks: this.count,
    swiperConfig: this.swiperProps,
    swiperEnabled: this.swiperEnabled
  };

  if (this.classHolder !== null) callbackData.statusClassHolder = this.classHolder;
  return callbackData;
};

KetchupSwiper.prototype.mergeObj = function() {
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

KetchupSwiper.prototype.destroy = function() {
  if (this.currSwiper) this.currSwiper.destroy(true, true);
  return this;
};
