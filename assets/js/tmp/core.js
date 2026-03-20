"use strict";

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var KetchupAccordion = function KetchupAccordion(opts, callback) {
  if (!(this instanceof KetchupAccordion)) return new KetchupAccordion(opts, callback);
  var defaults = {
    selectors: {
      trigger: '.faq-trigger',
      content: '.faq-answer'
    },
    classes: {
      triggerActive: 'expanded',
      contentActive: 'expanded'
    },
    config: {
      debounceTime: 20,
      addClasses: true,
      initialActive: null,
      offClick: false
    }
  };
  var userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.init();
  return this;
};

KetchupAccordion.prototype.init = function () {
  var self = this;
  var selectors = this.props.selectors;
  var initialActive = this.props.config.hasOwnProperty('initialActive') ? this.props.config.initialActive : null;
  var debounceTime = this.props.config.debounceTime;
  this.triggers = Array.prototype.slice.call(document.querySelectorAll(selectors.trigger));
  this.contents = Array.prototype.slice.call(document.querySelectorAll(selectors.content));
  this.boundToggle = this.toggleItem.bind(this);
  this.initTriggers();
  window.addEventListener('resize', self.debounceFunc.call(self, self.reset, debounceTime, false), false);
  if (initialActive !== null) this.setActive(initialActive);
  if (this.props.config.offClick === true) this.initOffclick();
  return this;
};

KetchupAccordion.prototype.debounceFunc = function (func, wait, immediate) {
  var self = this;
  var timeout;
  return function () {
    var context = self,
        args = arguments;

    var later = function later() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };

    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};

KetchupAccordion.prototype.initOffclick = function () {
  var self = this;
  var props = this.props;
  var initialActive = this.props.config.initialActive || null;
  document.addEventListener('click', function (event) {
    var clickedTrigger = false;
    var clickedContent = false;
    self.triggers.map(function (trigger) {
      if (trigger.contains(event.target)) {
        clickedTrigger = true;
      }
    });
    self.contents.map(function (trigger) {
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

KetchupAccordion.prototype.setActive = function (index) {
  var self = this;
  var classProps = this.props.classes;
  if (index > self.triggers.length || index > self.contents.length) return this;

  if (this.props.config.addClasses === true) {
    this.switchClass(self.triggers, index, classProps.triggerActive);
    this.switchClass(self.contents, index, classProps.contentActive);
  }

  this.toggleHeight(index);
};

KetchupAccordion.prototype.initTriggers = function () {
  var self = this;
  self.triggers.forEach(function (trigger) {
    trigger.addEventListener('click', self.boundToggle, false);
  });
  return this;
};

KetchupAccordion.prototype.destroyTriggers = function () {
  var self = this;
  self.triggers.forEach(function (trigger) {
    trigger.removeEventListener('click', self.boundToggle, false);
  });
  return this;
};

KetchupAccordion.prototype.toggleItem = function (e) {
  var activeIndex = this.triggers.indexOf(e.currentTarget);
  var activeContent = this.contents[activeIndex];
  var alreadyActive = activeContent.hasAttribute('style') && parseFloat(activeContent.style.height) > 1;

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

KetchupAccordion.prototype.reset = function () {
  var self = this;
  var props = self.props.classes;
  self.contents.map(function (content) {
    content.classList.remove(props.contentActive);
    content.removeAttribute('style');
  });
  self.triggers.map(function (trigger) {
    trigger.classList.remove(props.triggerActive);
  });
  if (this.props.config.initialActive !== null) this.setActive(this.props.config.initialActive);
  return this;
};

KetchupAccordion.prototype.toggleHeight = function (index) {
  var targetContent = this.contents[index];
  var contentHeight = targetContent.scrollHeight;
  this.contents.map(function (content) {
    if (content.hasAttribute('style')) {
      content.style.height = '0' + 'px';
    }

    targetContent.style.height = contentHeight + 'px';
    return this;
  });
};

KetchupAccordion.prototype.switchClass = function (elems, itemIndex, activeClass) {
  elems.forEach(function (item, index) {
    if (item.classList.contains(activeClass)) {
      item.classList.remove(activeClass);
    } else {
      item.classList.remove(activeClass);
      if (itemIndex === index) item.classList.add(activeClass);
    }
  });
  return this;
};

KetchupAccordion.prototype.destroy = function () {
  var self = this;
  self.reset();
  self.destroyTriggers();
  return this;
};

KetchupAccordion.prototype.mergeObj = function () {
  var self = this;
  var extended = {};
  var deep = false;
  var i = 0;
  var length = arguments.length;

  if (Object.prototype.toString.call(arguments[0]) === '[object Boolean]') {
    deep = arguments[0];
    i++;
  }

  var merge = function merge(obj) {
    for (var prop in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, prop)) {
        if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
          extended[prop] = self.mergeObj(true, extended[prop], obj[prop]);
        } else {
          extended[prop] = obj[prop];
        }
      }
    }
  };

  for (i = 0; i < length; i++) {
    var obj = arguments[i];
    merge(obj);
  }

  return extended;
};

var TimedAction = function () {
  function TimedAction(userProps) {
    _classCallCheck(this, TimedAction);

    this.userProps = userProps || {};
    var defaults = {
      time: 10,
      sskey: 'timed-popout',
      interval: 1000,
      onEveryStep: 2,
      autoInit: true,
      destroyOnEnd: true,
      on: {
        end: null,
        increment: null,
        every: null
      }
    };
    this.boundUpdate = this.updateTime.bind(this);
    this.props = this.mergeDeep(defaults, this.userProps);
    this.clear = this.clearCount.bind(this);
    this.stop = this.stopCount.bind(this);
    this.destroy = this.destroyCounter.bind(this);
    this.start = this.init.bind(this);
    var hasEnded = sessionStorage.getItem(this.props.sskey);
    if (hasEnded && parseInt(hasEnded) === this.props.time) return;
    if (this.props.autoInit && typeof this.props.autoInit === 'boolean') this.boundUpdate();
  }

  _createClass(TimedAction, [{
    key: "updateTime",
    value: function updateTime() {
      var sskey = this.props.sskey;
      var existingSskey = sessionStorage.getItem(sskey);
      var time = existingSskey ? existingSskey : 0;
      var nextInterval = parseInt(time) + 1;
      var payload = {
        current: this.props.time,
        next: nextInterval
      };

      if (this.props.on.increment) {
        payload.source = 'increment';
        this.props.on.increment(payload);
      }

      if (nextInterval % this.props.onEveryStep == 0) {
        if (this.props.on.every) {
          payload.source = 'every';
          this.props.on.every(payload);
        }
      }

      if (parseInt(time) >= parseInt(this.props.time)) {
        if (this.props.destroyOnEnd === true) this.destroy();

        if (this.props.on.end) {
          payload.source = 'end';
          this.props.on.end(payload);
        }

        return;
      }

      sessionStorage.setItem(this.props.sskey, nextInterval);
      this.timeoutRef = setTimeout(this.boundUpdate, this.props.interval);
    }
  }, {
    key: "clearCount",
    value: function clearCount() {
      sessionStorage.setItem(this.props.sskey, 0);
    }
  }, {
    key: "init",
    value: function init() {
      this.boundUpdate();
    }
  }, {
    key: "stopCount",
    value: function stopCount() {
      clearInterval(this.timeoutRef);
    }
  }, {
    key: "destroyCounter",
    value: function destroyCounter() {
      this.stopCount();
      sessionStorage.removeItem(this.props.sskey);
    }
  }, {
    key: "mergeDeep",
    value: function mergeDeep(target, source) {
      var _this = this;

      function isObject(item) {
        return item && _typeof(item) === 'object' && !Array.isArray(item) && item !== null;
      }

      if (isObject(target) && isObject(source)) {
        Object.keys(source).forEach(function (key) {
          if (isObject(source[key])) {
            if (!target[key] || !isObject(target[key])) {
              target[key] = source[key];
            }

            _this.mergeDeep(target[key], source[key]);
          } else {
            Object.assign(target, _defineProperty({}, key, source[key]));
          }
        });
      }

      return target;
    }
  }, {
    key: "getProps",
    get: function get() {
      return this.props;
    }
  }]);

  return TimedAction;
}();

var Cookie = function () {
  function Cookie() {
    _classCallCheck(this, Cookie);

    this.expDays = 'Thu, 01 Jan 1970 00:00:00 GMT';
    this.fetch = this.fetch.bind(this);
    this.add = this.add.bind(this);
    this.get = this.get.bind(this);
    this.delete = this.delete.bind(this);
    this.formatted = this.fetch();
  }

  _createClass(Cookie, [{
    key: "fetch",
    value: function fetch() {
      var cookies = document.cookie.split(';');
      var result = [];
      cookies.forEach(function (cookie) {
        var src = cookie.trim();
        var cookieName = src.split('=')[0];
        var cookieContent = src.split('=');
        cookieContent.shift();
        cookieContent = cookieContent.join('=');
        var formatted = {
          name: cookieName,
          content: cookieContent
        };
        result.push(formatted);
      });
      return result;
    }
  }, {
    key: "add",
    value: function add(name, content) {
      var expDays = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 365;
      var props = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';
      if (!name || !content) return this;
      var d = new Date();
      d.setTime(d.getTime() + expDays * 24 * 60 * 60 * 1000);
      document.cookie = "".concat(name, "=").concat(content, ";expires=").concat(d.toUTCString(), ";").concat(props, "path=/");
      this.formatted = this.fetch();
      return this;
    }
  }, {
    key: "get",
    value: function get() {
      var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      var arr = this.formatted;
      var result = arr.filter(function (item) {
        return item.name === name;
      });
      if (result.length === 0) return null;
      return result[0];
    }
  }, {
    key: "delete",
    value: function _delete() {
      var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      var domain = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : window.location.hostname;
      var target = this.get(name);
      if (!target) return null;
      document.cookie = "".concat(target.name, "='';expires=").concat(this.expDays, ";path=/;domain=").concat(domain, ";");
      this.formatted = this.fetch();
      return target;
    }
  }]);

  return Cookie;
}();

var PrivacyManager = function () {
  function PrivacyManager() {
    var userProps = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

    _classCallCheck(this, PrivacyManager);

    this.defaults = {
      expDays: 'Thu, 01 Jan 1970 00:00:00 GMT',
      saveAsCookie: true,
      cookieExp: 365,
      savePreferences: true,
      key: 'privacy-preferences',
      form: null,
      on: {
        init: null,
        update: null,
        restore: null
      },
      items: []
    };
    this.props = this.mergeDeep(this.defaults, userProps);
    this.savedPreferences = [];
    var form = this.props.form;
    if (!(form instanceof Element) || !form) throw new Error('"form" should be DOM object');
    this.init = this.init.bind(this);
    this.updateFromSave = this.updateFromSave.bind(this);
    this.updatePreferences = this.updatePreferences.bind(this);
    this.updateSaved = this.updateSaved.bind(this);
    this.perfomAction = this.performAction.bind(this);
    this.restoreDefaults = this.restoreDefaults.bind(this);
    this.fetchCookies = this.fetchCookies.bind(this);
    this.addCookie = this.addCookie.bind(this);
    this.getCookie = this.getCookie.bind(this);
    this.deleteCookie = this.deleteCookie.bind(this);
    this.payload = this.payload.bind(this);
    this.formatted = this.fetchCookies();
    this.init();
  }

  _createClass(PrivacyManager, [{
    key: "init",
    value: function init() {
      var savedCookie = this.getCookie(this.props.key);
      var savedStorage = localStorage.getItem(this.props.key);

      if (this.props.saveAsCookie) {
        this.savedPreferences = savedCookie ? JSON.parse(savedCookie.content) : [];
        if (savedStorage) localStorage.removeItem(this.props.key);
      }

      if (!this.props.saveAsCookie) {
        this.savedPreferences = savedStorage ? JSON.parse(savedStorage) : [];
        if (savedCookie) this.deleteCookie(this.props.key);
      }

      var form = this.props.form;
      form.addEventListener('submit', this.updatePreferences, false);
      var savedPref = this.savedPreferences;
      if (savedPref.length > 0) this.updateFromSave();
      this.updatePreferences();
      this.payload(this.props.on.init);
    }
  }, {
    key: "updateFromSave",
    value: function updateFromSave() {
      var form = this.props.form;
      var saved = this.savedPreferences;
      saved.forEach(function (savedItem) {
        var item = form[savedItem.name];

        if (item) {
          item.checked = savedItem.checked;
        }
      });
    }
  }, {
    key: "payload",
    value: function payload(func) {
      var isCookie = this.props.saveAsCookie;
      var data = isCookie ? this.getCookie(this.props.key) : localStorage.getItem(this.props.key);
      var result = null;
      if (data) result = isCookie ? data.content : data;
      var payload = {
        form: this.props.form,
        id: this.props.key,
        data: result ? JSON.parse(result) : []
      };
      if (func) func(payload);
    }
  }, {
    key: "updatePreferences",
    value: function updatePreferences(e) {
      var _this2 = this;

      if (e) e.preventDefault();
      var form = this.props.form;
      this.savedPreferences = [];
      this.props.items.forEach(function (item) {
        var name = item.name;
        if (name && form[name]) _this2.performAction(form[name].checked, item);
      });
      if (this.props.savePreferences) this.updateSaved();
      this.payload(this.props.on.update);
    }
  }, {
    key: "updateSaved",
    value: function updateSaved() {
      var cookieExp = this.props.cookieExp;
      var key = this.props.key;

      if (this.props.saveAsCookie) {
        this.addCookie(key, JSON.stringify(this.savedPreferences), cookieExp);
      } else {
        localStorage.setItem(key, JSON.stringify(this.savedPreferences));
      }
    }
  }, {
    key: "performAction",
    value: function performAction(checked, config) {
      var isChecked = checked || false;
      var onApprove = config.onApprove;
      var onDeny = config.onDeny;
      var name = config.name;
      var item = {
        name: name,
        checked: isChecked
      };
      this.savedPreferences.push(item);

      if (isChecked) {
        if (onApprove) onApprove(item);
      } else {
        if (onDeny) onDeny(item);
      }

      if (config.onAction) config.onAction(item);
    }
  }, {
    key: "restoreDefaults",
    value: function restoreDefaults() {
      var _this3 = this;

      var form = this.props.form;
      var items = this.props.items;

      if (this.props.saveAsCookie) {
        this.deleteCookie(this.props.key);
      } else {
        localStorage.removeItem(this.props.key);
      }

      var preferences = [];
      items.forEach(function (item) {
        var checkbox = form[item.name];

        if (checkbox) {
          var output = {
            name: item.name,
            checked: item.checked
          };
          preferences.push(output);
          checkbox.checked = item.checked;

          _this3.performAction(form[item.name].checked, item);
        }
      });
      this.savedPreferences = preferences;
      if (this.props.savePreferences) this.updateSaved();
      this.payload(this.props.on.restore);
    }
  }, {
    key: "fetchCookies",
    value: function fetchCookies() {
      var cookies = document.cookie.split(';');
      var result = [];
      cookies.forEach(function (cookie) {
        var src = cookie.trim();
        var cookieName = src.split('=')[0];
        var cookieContent = src.split('=');
        cookieContent.shift();
        cookieContent = cookieContent.join('=');
        var formatted = {
          name: cookieName,
          content: cookieContent
        };
        result.push(formatted);
      });
      return result;
    }
  }, {
    key: "addCookie",
    value: function addCookie(name, content) {
      var expDays = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 365;
      var props = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';
      if (!name || !content) return this;
      var d = new Date();
      d.setTime(d.getTime() + expDays * 24 * 60 * 60 * 1000);
      document.cookie = "".concat(name, "=").concat(content, ";expires=").concat(d.toUTCString(), ";").concat(props, "path=/");
      this.formatted = this.fetchCookies();
      return this;
    }
  }, {
    key: "getCookie",
    value: function getCookie() {
      var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      var arr = this.formatted;
      var result = arr.filter(function (item) {
        return item.name === name;
      });
      if (result.length === 0) return null;
      return result[0];
    }
  }, {
    key: "deleteCookie",
    value: function deleteCookie() {
      var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      var target = this.getCookie(name);
      if (!target) return null;
      document.cookie = "".concat(target.name, "='';expires=").concat(this.props.expDays, ";path=/");
      this.formatted = this.fetchCookies();
      return target;
    }
  }, {
    key: "mergeDeep",
    value: function mergeDeep(target, source) {
      var _this4 = this;

      function isObject(item) {
        return item && _typeof(item) === 'object' && !Array.isArray(item) && item !== null;
      }

      if (isObject(target) && isObject(source)) {
        Object.keys(source).forEach(function (key) {
          if (isObject(source[key])) {
            if (!target[key] || !isObject(target[key])) {
              target[key] = source[key];
            }

            _this4.mergeDeep(target[key], source[key]);
          } else {
            Object.assign(target, _defineProperty({}, key, source[key]));
          }
        });
      }

      return target;
    }
  }, {
    key: "getProps",
    get: function get() {
      return this.props;
    }
  }]);

  return PrivacyManager;
}();

var KetchupSwiper = function KetchupSwiper(opts, callback) {
  if (!(this instanceof KetchupSwiper)) return new KetchupSwiper(opts, callback);
  var defaults = {
    enableMedia: '(max-width: 980px)',
    targetSwiper: '.conditional-swiper',
    matchBoth: false,
    classPrefix: null
  };
  var userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.currSwiper = null;
  this.swiperProps = {};
  this.count = Array.prototype.slice.call(document.querySelectorAll(this.props.targetSwiper + ' .swiper-slide'));
  this.cap = this.props.cap || this.count.length + 1;
  this.swiperMedia = window.matchMedia(this.props.enableMedia);
  this.classHolder = this.props.classHolder ? document.querySelector(this.props.classHolder) : null;
  this.swiperEnabled = false;
  var swiperSettingsDefaults = {
    slideClass: 'swiper-slide',
    a11y: {
      enabled: false
    },
    on: {
      init: function init() {
        setTimeout(function () {
          if (typeof Event === 'function') {
            window.dispatchEvent(new Event('resize'));
          } else {
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

KetchupSwiper.prototype.init = function () {
  var self = this;
  this.swiperMedia.addListener(this.swiperAction.bind(self));
  this.swiperAction.call(self, this.swiperMedia);
  return this;
};

KetchupSwiper.prototype.swiperAction = function (swiperMedia) {
  var condition = this.props.matchBoth === true ? swiperMedia.matches && this.cap <= this.count.length : swiperMedia.matches || this.cap <= this.count.length;

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

KetchupSwiper.prototype.switchClass = function (add, remove) {
  var classPrefix = this.props.classPrefix === null ? '' : this.props.classPrefix;
  this.classHolder.classList.remove(classPrefix + remove);
  this.classHolder.classList.add(classPrefix + add);
  return this;
};

KetchupSwiper.prototype.payload = function () {
  var callbackData = {
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

KetchupSwiper.prototype.mergeObj = function () {
  var self = this;
  var extended = {};
  var deep = false;
  var i = 0;
  var length = arguments.length;

  if (Object.prototype.toString.call(arguments[0]) === '[object Boolean]') {
    deep = arguments[0];
    i++;
  }

  var merge = function merge(obj) {
    for (var prop in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, prop)) {
        if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
          extended[prop] = self.mergeObj(true, extended[prop], obj[prop]);
        } else {
          extended[prop] = obj[prop];
        }
      }
    }
  };

  for (i = 0; i < length; i++) {
    var obj = arguments[i];
    merge(obj);
  }

  return extended;
};

KetchupSwiper.prototype.destroy = function () {
  if (this.currSwiper) this.currSwiper.destroy(true, true);
  return this;
};

var KetchupModal = function KetchupModal(opts, callback) {
  if (!(this instanceof KetchupModal)) return new KetchupModal(opts, callback);
  var defaults = {
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
      element: '.km-modal-content'
    }
  };
  var userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.init();
  return this;
};

KetchupModal.prototype.init = function () {
  var self = this;
  var selectors = this.props.selectors;
  this.triggers = Array.prototype.slice.call(document.querySelectorAll(selectors.trigger));
  var contentBlock = document.querySelector(selectors.modal);
  if (!contentBlock) return;
  this.contentBlock = contentBlock;
  this.boundToggleModal = this.toggleModal.bind(self);
  this.triggers.map(function (trigger) {
    trigger.addEventListener('click', self.boundToggleModal, false);
  });
  if (this.props.offClick.enabled === true) this.initOffclick();
};

KetchupModal.prototype.initOffclick = function () {
  var self = this;
  var props = this.props;
  var target = props.offClick.element === null ? this.contentBlock : document.querySelector(props.offClick.element);
  document.addEventListener('click', function (event) {
    var clickedTrigger = false;
    self.triggers.map(function (trigger) {
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

KetchupModal.prototype.destroy = function () {
  var self = this;
  this.triggers.map(function (trigger) {
    trigger.removeEventListener('click', self.boundToggleModal, false);
  });
  this.reset();
};

KetchupModal.prototype.reset = function () {
  var classProps = this.props.classes;
  var modal = this.contentBlock;
  if (classProps.modalActive !== null) modal.classList.remove(classProps.modalActive);
  if (classProps.bodyClass !== null) document.body.classList.remove(classProps.bodyClass);

  if (classProps.triggerActive !== null) {
    this.triggers.map(function (trigger) {
      trigger.classList.remove(classProps.triggerActive);
    });
  }
};

KetchupModal.prototype.toggleModal = function () {
  var classProps = this.props.classes;
  var modal = this.contentBlock;
  if (!modal) return;
  if (classProps.modalActive !== null) modal.classList.toggle(classProps.modalActive);
  if (classProps.bodyClass !== null) document.body.classList.toggle(classProps.bodyClass);

  if (classProps.triggerActive !== null) {
    this.triggers.map(function (trigger) {
      trigger.classList.toggle(classProps.triggerActive);
    });
  }

  this.servePayload('button');
};

KetchupModal.prototype.servePayload = function (source) {
  if (!this.callback) return;
  var payload = {
    triggers: this.triggers,
    modal: this.contentBlock,
    classes: this.props.classes,
    modalState: this.contentBlock.classList.contains(this.props.classes.modalActive) ? 'open' : 'closed',
    offClickEnabled: this.props.offClick.enabled
  };
  if (source) payload.source = source;
  this.callback(payload);
};

KetchupModal.prototype.mergeObj = function () {
  var self = this;
  var extended = {};
  var deep = false;
  var i = 0;
  var length = arguments.length;

  if (Object.prototype.toString.call(arguments[0]) === '[object Boolean]') {
    deep = arguments[0];
    i++;
  }

  var merge = function merge(obj) {
    for (var prop in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, prop)) {
        if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
          extended[prop] = self.mergeObj(true, extended[prop], obj[prop]);
        } else {
          extended[prop] = obj[prop];
        }
      }
    }
  };

  for (i = 0; i < length; i++) {
    var obj = arguments[i];
    merge(obj);
  }

  return extended;
};

var KetchupVideoBg = function KetchupVideoBg(opts, callback) {
  if (!(this instanceof KetchupVideoBg)) return new KetchupVideoBg(opts, callback);
  var defaults = {
    customControls: '(min-width:981px)',
    context: {
      videoContainer: null,
      video: null,
      playBtn: null,
      pauseBtn: null,
      muteToggle: null
    },
    classes: {
      activeBtn: 'video-btn-active',
      customControlsClass: 'km-video-custom-controls',
      muteActive: 'km-video-mutted'
    },
    config: {
      initControls: true,
      autoplay: true
    }
  };
  var userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.mediaCheck = window.matchMedia(this.props.customControls);
  this.mediaMatches = false;
  this.videoContainer = this.props.context.videoContainer;
  this.videoElement = this.props.context.video;
  this.playBtn = this.props.context.playBtn;
  this.pauseBtn = this.props.context.pauseBtn;
  if (!this.videoContainer || !this.videoElement) return null;
  this.init();
  return this;
};

KetchupVideoBg.prototype.init = function () {
  var self = this;
  var muteClass = this.props.classes.muteActive;
  var muteToggle = this.props.context.muteToggle;
  this.mediaCheck.addListener(self.detectView.bind(self));
  this.detectView.call(self, self.mediaCheck);

  if (this.props.config.initControls === true && this.playBtn && this.pauseBtn) {
    self.controlBtnClasses();
    this.pauseBtn.addEventListener('click', function () {
      self.videoElement.pause();
      self.controlBtnClasses();
    }, false);
    this.playBtn.addEventListener('click', function () {
      self.videoElement.play();
      self.controlBtnClasses();
    }, false);
  }

  if (this.props.context.muteToggle) {
    this.props.context.muteToggle.addEventListener('click', function () {
      self.videoElement.muted = !self.videoElement.muted;
      if (muteClass && muteToggle) muteToggle.classList.toggle(muteClass);
    }, false);
  }

  if (muteClass && muteToggle && self.videoElement.muted) muteToggle.classList.add(muteClass);
  return this;
};

KetchupVideoBg.prototype.controlBtnClasses = function () {
  var videoElement = this.videoElement;
  var activeClass = this.props.classes.activeBtn;

  if (videoElement.paused === true) {
    this.playBtn.classList.add(activeClass);
    this.pauseBtn.classList.remove(activeClass);
  } else {
    this.pauseBtn.classList.add(activeClass);
    this.playBtn.classList.remove(activeClass);
  }
};

KetchupVideoBg.prototype.responsiveAction = function () {
  var videoContainer = this.videoContainer;
  var videoElement = this.videoElement;
  var containerClass = this.props.classes.customControlsClass;

  if (videoElement && videoElement.paused === false) {
    videoElement.pause();
  }

  if (containerClass) videoContainer.classList.remove(containerClass);
  videoElement.setAttribute('controls', 'controls');
  videoElement.removeAttribute('autoplay');
  videoElement.removeAttribute('style');
  videoElement.controlsList = 'nodownload';
  videoElement.load();
  videoElement.preload = 'auto';
};

KetchupVideoBg.prototype.desktopAction = function () {
  var videoContainer = this.videoContainer;
  var videoElement = this.videoElement;
  var containerClass = this.props.classes.customControlsClass;
  videoElement.removeAttribute('controls');
  if (containerClass) videoContainer.classList.add(containerClass);

  if (this.props.config.autoplay) {
    videoElement.autoplay = true;
    videoElement.play();
  }
};

KetchupVideoBg.prototype.detectView = function (query) {
  if (query.matches) {
    this.mediaMatches = true;
    this.desktopAction();
  } else {
    this.mediaMatches = false;
    this.responsiveAction();
  }

  if (this.props.config.initControls === true && this.playBtn && this.pauseBtn) {
    this.controlBtnClasses();
  }

  return this;
};

KetchupVideoBg.prototype.mergeObj = function () {
  var self = this;
  var extended = {};
  var deep = false;
  var i = 0;
  var length = arguments.length;

  if (Object.prototype.toString.call(arguments[0]) === '[object Boolean]') {
    deep = arguments[0];
    i++;
  }

  var merge = function merge(obj) {
    for (var prop in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, prop)) {
        if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
          extended[prop] = self.mergeObj(true, extended[prop], obj[prop]);
        } else {
          extended[prop] = obj[prop];
        }
      }
    }
  };

  for (i = 0; i < length; i++) {
    var obj = arguments[i];
    merge(obj);
  }

  return extended;
};

var BrowserDetector = function () {
  function BrowserDetector() {
    var unsupported = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

    _classCallCheck(this, BrowserDetector);

    this.browser = {};
    this.defaultsBrowsers = {
      Chrome: 70,
      Firefox: 60,
      IE: 11,
      Edge: 15,
      Opera: 50,
      Safari: 12
    };
    this.unsupportedBrowsers = Object.assign({}, this.defaultsBrowsers, unsupported);

    this._detectBrowser();
  }

  _createClass(BrowserDetector, [{
    key: "_detectBrowser",
    value: function _detectBrowser() {
      this.browser = function () {
        var ua = navigator.userAgent,
            tem,
            M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];

        if (/trident/i.test(M[1])) {
          tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
          return {
            name: 'IE',
            version: tem[1] || ''
          };
        }

        if (M[1] === 'Chrome') {
          tem = ua.match(/\b(OPR|Edge)\/(\d+)/);

          if (tem != null) {
            return {
              name: tem[1].replace('OPR', 'Opera'),
              version: tem[2]
            };
          }
        }

        M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];

        if ((tem = ua.match(/version\/(\d+)/i)) != null) {
          M.splice(1, 1, tem[1]);
        }

        return {
          name: M[0],
          version: M[1]
        };
      }();
    }
  }, {
    key: "isSupported",
    value: function isSupported() {
      if (this.unsupportedBrowsers.hasOwnProperty(this.browser.name)) {
        if (+this.browser.version > this.unsupportedBrowsers[this.browser.name]) {
          return true;
        }
      }

      return false;
    }
  }, {
    key: "browserList",
    get: function get() {
      return this.unsupportedBrowsers;
    }
  }, {
    key: "isIE",
    get: function get() {
      return this.browser.name === 'IE';
    }
  }, {
    key: "isEdge",
    get: function get() {
      return this.browser.name === 'Edge';
    }
  }, {
    key: "isMicrosoft",
    get: function get() {
      return this.isIE || this.isEdge;
    }
  }, {
    key: "isFirefox",
    get: function get() {
      return this.browser.name === 'Firefox';
    }
  }, {
    key: "isChrome",
    get: function get() {
      return this.browser.name === 'Chrome';
    }
  }, {
    key: "isSafari",
    get: function get() {
      return this.browser.name === 'Safari';
    }
  }, {
    key: "isAndroid",
    get: function get() {
      return /Android/i.test(navigator.userAgent);
    }
  }, {
    key: "isBlackBerry",
    get: function get() {
      return /BlackBerry/i.test(navigator.userAgent);
    }
  }, {
    key: "isWindowsMobile",
    get: function get() {
      return /IEMobile/i.test(navigator.userAgent);
    }
  }, {
    key: "isIOS",
    get: function get() {
      return /iPhone|iPad|iPod/i.test(navigator.userAgent);
    }
  }, {
    key: "isMobile",
    get: function get() {
      return this.isAndroid || this.isBlackBerry || this.isWindowsMobile || this.isIOS;
    }
  }, {
    key: "showInfo",
    get: function get() {
      var result = [{
        'Browser data:': this.browser
      }, {
        'Unsupported versions:': this.browserList
      }, {
        'Is browser supported?:': this.isSupported()
      }, {
        IE: this.isIE
      }, {
        Edge: this.isEdge
      }, {
        'Chrome:': this.isChrome
      }, {
        'Firefox:': this.isFirefox
      }, {
        'Safari:': this.isSafari
      }, {
        'Microsoft:': this.isMicrosoft
      }, {
        'Android:': this.isAndroid
      }, {
        'Blackberry:': this.isBlackBerry
      }, {
        'Windows mobile:': this.isWindowsMobile
      }, {
        'IOS:': this.isIOS
      }, {
        'Mobile device:': this.isMobile
      }];
      return JSON.stringify(result);
    }
  }]);

  return BrowserDetector;
}();

function gsap_animateIcons() {
  var blocks = _toConsumableArray(document.querySelectorAll('.km-icons-block'));

  blocks.forEach(function (block) {
    var title = block.querySelector('.km-icb-main-title');
    var iconTitle = block.querySelector('.km-icb-icon-title');
    var content = block.querySelector('.km-icb-content-wrap');

    var icons = _toConsumableArray(block.querySelectorAll('.km-icb-item'));

    var tl = gsap.timeline({
      scrollTrigger: {
        trigger: block,
        start: "top 90%",
        toggleActions: 'restart none resume reverse'
      }
    });
    if (title) tl.from(title, {
      opacity: 0
    });
    if (content) tl.from(content, {
      opacity: 0
    }, "+=0.2");
    if (iconTitle) tl.from(iconTitle, {
      opacity: 0
    }, "+=0.2");

    if (icons.length > 0) {
      tl.from(icons, {
        opacity: 0,
        scale: 0.1,
        duration: 0.3,
        stagger: '0.2'
      });
    }
  });
}

function gsap_scaleIn(block, items) {
  var blocks = _toConsumableArray(document.querySelectorAll(block));

  blocks.forEach(function (block) {
    var items = _toConsumableArray(block.querySelectorAll(items));

    if (items.length > 0) {
      gsap.from(items, {
        scrollTrigger: {
          trigger: block,
          start: "30% center",
          toggleActions: 'restart none resume none'
        },
        opacity: 0,
        scale: 0.1,
        duration: 0.3,
        stagger: '0.2'
      });
    }
  });
}

;

function gsap_Parallax(block, imageSel) {
  var blocks = _toConsumableArray(document.querySelectorAll(block));

  blocks.forEach(function (block) {
    var image = block.querySelector(imageSel);
    gsap.to(image, {
      yPercent: 20,
      ease: "none",
      scrollTrigger: {
        trigger: block,
        scrub: true
      }
    });
  });
}

function gsap_enterPos(block, item) {
  var blocks = _toConsumableArray(document.querySelectorAll(block));

  blocks.forEach(function (block) {
    var ele = block.querySelector(item);
    var isRight = block.classList.contains('image-location-right') ? true : false;
    var isContent = item === '.km-acb-b' ? true : false;
    var props = {
      opacity: 0,
      ease: "power3",
      duration: 1,
      scrollTrigger: {
        trigger: block,
        start: '-=5% center',
        end: '+=10%',
        toggleActions: 'play none none reverse'
      }
    };
    props.xPercent = isRight ? isContent ? -20 : 20 : isContent ? 20 : -20;
    gsap.from(ele, props);
  });
}

function gsap_watermark(block) {
  var blocks = _toConsumableArray(document.querySelectorAll(block));

  blocks.forEach(function (block) {
    var props = {
      scrollTrigger: {
        trigger: block,
        start: 'top center',
        end: '+=100',
        onEnter: function onEnter() {
          return block.classList.add('km-ani-on');
        }
      }
    };
    gsap.from(block, props);
  });
}

function updateSwiperColours(element, slug) {
  addSlug(element, '.swn-icon svg', ['has-' + slug + '-fill']);
  addSlug(element, '.swiper-pagination-bullet', ['has-' + slug + '-border-colour']);
  addSlug(element, '.swiper-pagination-bullet svg', ['has-' + slug + '-fill']);
  addSlug(element, '.fn-fraction', ['has-' + slug + '-colour']);
}

var DOMAnimations = {
  slideUp: function slideUp(element) {
    var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;
    return new Promise(function (resolve, reject) {
      element.style.height = element.offsetHeight + 'px';
      element.style.transitionProperty = "height, margin, padding";
      element.style.transitionDuration = duration + 'ms';
      element.offsetHeight;
      element.style.overflow = 'hidden';
      element.style.height = 0;
      element.style.paddingTop = 0;
      element.style.paddingBottom = 0;
      element.style.marginTop = 0;
      element.style.marginBottom = 0;
      window.setTimeout(function () {
        element.style.display = 'none';
        element.style.removeProperty('height');
        element.style.removeProperty('padding-top');
        element.style.removeProperty('padding-bottom');
        element.style.removeProperty('margin-top');
        element.style.removeProperty('margin-bottom');
        element.style.removeProperty('overflow');
        element.style.removeProperty('transition-duration');
        element.style.removeProperty('transition-property');
        resolve(false);
      }, duration);
    });
  },
  slideDown: function slideDown(element) {
    var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;
    var blockType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'block';
    return new Promise(function (resolve, reject) {
      element.style.removeProperty('display');
      var display = window.getComputedStyle(element).display;
      if (display === 'none') display = blockType;
      element.style.display = display;
      var height = element.offsetHeight;
      element.style.overflow = 'hidden';
      element.style.height = 0;
      element.style.paddingTop = 0;
      element.style.paddingBottom = 0;
      element.style.marginTop = 0;
      element.style.marginBottom = 0;
      element.offsetHeight;
      element.style.transitionProperty = "height, margin, padding";
      element.style.transitionDuration = duration + 'ms';
      element.style.height = height + 'px';
      element.style.removeProperty('padding-top');
      element.style.removeProperty('padding-bottom');
      element.style.removeProperty('margin-top');
      element.style.removeProperty('margin-bottom');
      window.setTimeout(function () {
        element.style.removeProperty('height');
        element.style.removeProperty('overflow');
        element.style.removeProperty('transition-duration');
        element.style.removeProperty('transition-property');
      }, duration);
    });
  },
  slideToggle: function slideToggle(element) {
    var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;
    var blockType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'block';

    if (window.getComputedStyle(element).display === 'none') {
      return this.slideDown(element, duration, blockType);
    } else {
      return this.slideUp(element, duration);
    }
  }
};

function aniLabel() {
  var inputs = Array.prototype.slice.call(document.querySelectorAll('.ninja-forms-field.nf-element'));
  inputs.forEach(function (input) {
    input.addEventListener('focus', function () {
      labelAction(input, true);
    }, false);
    input.addEventListener('blur', function () {
      labelAction(input, false);
    }, false);
  });
}

function labelAction(input, isActive) {
  var target = input.closest('.nf-field');
  var lbl = target.querySelector('.nf-field-label');
  var action = isActive ? 'add' : 'remove';
  var activeClass = 'ninja-lbl-active';

  if (lbl) {
    if (input.value.trim() === '') {
      lbl.classList[action](activeClass);
    } else {
      lbl.classList.add(activeClass);
    }
  }
}

function detectProp(sel, target, attr, cb) {
  var elements = Array.prototype.slice.call(document.querySelectorAll(sel));
  elements.forEach(function (element) {
    var source = element.querySelector(target);

    if (source && source.hasAttribute(attr)) {
      var result = source.getAttribute(attr);
      if (cb) cb(element, result);
    }
  });
}

function addSlug(parent, sel, classNames) {
  var items = Array.prototype.slice.call(parent.querySelectorAll(sel));
  items.forEach(function (item) {
    classNames.forEach(function (className) {
      item.classList.add(className);
    });
  });
}

function loadMorePosts(config, cb) {
  var props = config || {};
  var btnSel = props.btn || '.load-more:not(.disabled)';
  var targetSel = props.appendTo || '.posts-list';
  var loaderSel = props.loader || '.posts-loader';
  var totalAttr = props.totalAttr || 'total';
  var isDev = window.location.host === props.devHost || window.location.host === 'www.ketchupdevelopment.co.uk';
  var sub = isDev ? '/' + props.devSub + '/' || '/' : '/';
  var ajaxUrl = props.ajaxEndpoint || window.location.origin + sub + 'wp-admin/admin-ajax.php';
  var btn = document.querySelector(btnSel);
  var target = document.querySelector(targetSel);
  var sessionKey = props.sessionKey || 'km-pg';
  var loaderActiveClass = props.loaderActive || 'active';
  var disabledBtnClass = props.btnDisabledClass || 'disabled';
  if (!btn || !target) return;
  var maxPages = parseInt(btn.dataset[totalAttr]);
  var savedPage = sessionStorage.getItem(sessionKey);
  var loader = document.querySelector(loaderSel);
  if (!maxPages) throw new Error(targetSel + ' is missing data-' + totalAttr + 'attribute ');
  if (savedPage) fetchPosts(true);
  if (!savedPage) sessionStorage.setItem(sessionKey, 1);
  btn.addEventListener('click', function (e) {
    e.preventDefault();
    fetchPosts(false);
  }, false);

  function fetchPosts(initialFetch) {
    var page = parseInt(sessionStorage.getItem(sessionKey));
    var params = new URLSearchParams();
    params.append('action', 'load_more');
    var step = page;
    params.set('page', step);
    if (loader) loader.classList.add(loaderActiveClass);
    btn.classList.add(disabledBtnClass);
    axios.post(ajaxUrl, params).then(function (res) {
      if (maxPages - 1 === step) btn.parentNode.removeChild(btn);

      if (!initialFetch) {
        step++;
        sessionStorage.setItem(sessionKey, step);
      }

      if (loader) loader.classList.remove(loaderActiveClass);
      btn.classList.remove(disabledBtnClass);
      target.insertAdjacentHTML('beforeend', res.data);
      if (cb) cb();
    });
  }

  if (maxPages === 1) {
    if (btn) btn.parentNode.removeChild(btn);
  }

  if (savedPage && savedPage > 1) {
    while (target.firstChild) {
      target.removeChild(target.firstChild);
    }

    var _loop = function _loop(i) {
      var params = new URLSearchParams();
      params.append('action', 'load_more');
      params.set('page', i - 1);
      if (loader) loader.classList.add(loaderActiveClass);
      btn.classList.add(disabledBtnClass);
      axios.post(ajaxUrl, params).then(function (res) {
        target.insertAdjacentHTML('beforeend', res.data);

        if (maxPages === i) {
          btn.parentNode.removeChild(btn);
          btn.classList.remove(disabledBtnClass);
        }

        if (loader) loader.classList.remove(loaderActiveClass);
      });
    };

    for (var i = 1; i <= savedPage; i++) {
      _loop(i);
    }
  }
}

function scrollClass(config, cb) {
  var props = config || {};
  var element = props.element || document.querySelector('.top-bar');
  var isAdmin = document.body.classList.contains('wp-admin');
  var dist = props.addAfter || 100;
  var className = props.class || 'active';
  if (!element || isAdmin) return;
  var payload = {
    element: element,
    dist: dist,
    class: className,
    action: null
  };
  var state;

  var checkVisibility = function checkVisibility() {
    if (window.pageYOffset >= dist) {
      element.classList.add(className);
      runCb('add');
      state = 'add';
    } else {
      element.classList.remove(className);
      runCb('remove');
      state = 'remove';
    }
  };

  function runCb(currAction) {
    if (currAction === state) return;

    if (cb) {
      payload.action = window.pageYOffset >= dist ? 'add' : 'remove';
      cb(payload);
    }
  }

  checkVisibility();
  window.addEventListener('scroll', checkVisibility);
}

function scrollToElement(btn, speed, offset) {
  var topOffset = offset || 0;
  var aniSpeed = speed || 500;
  jQuery(btn).click(function () {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') || location.hostname == this.hostname) {
      var target = jQuery(this.hash);
      target = target.length ? target : jQuery('[name=' + this.hash.slice(1) + ']');

      if (target.length) {
        jQuery('html,body').animate({
          scrollTop: target.offset().top - topOffset
        }, aniSpeed);
        return false;
      }
    }
  });
}

function kmIntersect() {
  var items = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var userProps = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {
    observerProps: {}
  };
  if (!("IntersectionObserver" in window)) return;
  var defaultProps = {
    observerProps: {
      threshold: [0.5]
    },
    on: undefined,
    off: undefined
  };

  var props = _objectSpread(_objectSpread({}, defaultProps), userProps);

  var observerProps = _objectSpread(_objectSpread({}, defaultProps.observerProps), userProps.observerProps);

  var observerInst = new IntersectionObserver(observe, observerProps);

  function observe(items, observer) {
    items.forEach(function (item) {
      if (item.isIntersecting) {
        if (props.on) props.on(item, observerInst);
      } else {
        if (props.off) props.off(item, observerInst);
      }
    });
  }

  items.forEach(function (item) {
    return observerInst.observe(item);
  });
}

function fitVideo(videoContainer, videoElement) {
  function updateVideo() {
    var container_width = videoContainer.offsetWidth;
    var container_height = videoContainer.offsetHeight;
    videoElement.style.height = 'auto';
    videoElement.style.width = container_width + 'px';

    if (videoElement.offsetHeight < container_height) {
      videoElement.style.height = container_height + 'px';
      videoElement.style.width = 'auto';
    }

    videoElement.style.top = (videoElement.offsetHeight - container_height) / 2 * -1 + 'px';
    videoElement.style.left = (videoElement.offsetWidth - container_width) / 2 * -1 + 'px';
  }

  window.addEventListener('load', updateVideo);
  window.addEventListener('resize', updateVideo);
}

;

function loadLazyVideo(item, observer) {
  var lazyClass = 'lazy-bg-video';
  var video = item.target;
  var fitParent = video.hasAttribute('fitparent');
  var autoPlays = video.hasAttribute('autoplays');

  if (video.classList.contains(lazyClass)) {
    for (var source in video.children) {
      var videoSource = video.children[source];

      if (typeof videoSource.tagName === "string" && videoSource.tagName === "SOURCE") {
        videoSource.src = videoSource.dataset.src;
      }
    }

    video.load();
    video.classList.remove(lazyClass);

    if (fitParent) {
      video.parentElement.classList.add('js-controlled-video');
      fitVideo(video.parentElement, video);
    }

    if (autoPlays) {
      video.play();
    }
  } else {
    if (autoPlays) video.play();
  }

  video.classList.remove('km-video-paused');
}

function pauseLazyVideo(item, observer) {
  var video = item.target;
  var autoPlays = video.hasAttribute('autoplays');
  if (video.tagName !== "VIDEO") return;

  if (autoPlays) {
    video.pause();
    video.classList.add('km-video-paused');
  }
}

function lazyBgImage(item, observer) {
  var image = item.target;
  image.classList.remove("lazy-bg-image");
  observer.unobserve(image);
}

function animateValue(obj, start, end) {
  var duration = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 3000;
  var startTimestamp = null;

  var step = function step(timestamp) {
    if (!startTimestamp) startTimestamp = timestamp;
    var progress = Math.min((timestamp - startTimestamp) / duration, 1);
    obj.innerHTML = Math.floor(progress * (end - start) + start);

    if (progress < 1) {
      window.requestAnimationFrame(step);
    }
  };

  window.requestAnimationFrame(step);
}

function pushMenu() {
  var classes = {
    menu: 'push-menu',
    hasChildren: 'menu-item-has-children',
    active: 'active',
    menuTrigger: 'push-menu-trigger',
    subMenu: 'sub-menu',
    subMenuTrigger: 'sub-menu-trigger'
  };
  var html = document.documentElement;
  var menu = document.getElementsByClassName(classes.menu)[0];
  var menuTriggers = Array.from(document.getElementsByClassName(classes.menuTrigger));
  var items = Array.from(document.getElementsByClassName(classes.hasChildren));

  var triggerElement = function triggerElement() {
    var trigger = document.createElement('button');
    var icon = document.createElement('span');
    icon.className = 'km-icon-arrow-right';
    trigger.appendChild(icon);
    trigger.className = classes.subMenuTrigger;
    trigger.addEventListener('click', function (e) {
      var ele = e.currentTarget;
      var target = ele.nextElementSibling;

      if (target.classList.contains(classes.subMenu)) {
        ele.classList.toggle(classes.active);
        target.classList.toggle(classes.active);
        DOMAnimations.slideToggle(target);
      }
    }, false);
    return trigger;
  };

  items.map(function (item) {
    var dropdown = item.querySelector('ul');
    if (!dropdown) return;
    dropdown.classList.add(classes.subMenu);
    item.insertBefore(triggerElement(), item.firstChild.nextSibling);
  });
  menuTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function (e) {
      html.classList.toggle('push-menu-active');
      document.addEventListener('click', offClick, false);
      var isActive = html.classList.contains('push-menu-active');

      if (isActive) {
        window.addEventListener('resize', reset, false);
      } else {
        reset();
        window.removeEventListener('resize', reset, false);
      }
    }, false);
  });

  function reset() {
    html.classList.remove('push-menu-active');
    items.forEach(function (item) {
      var subMenu = item.getElementsByClassName(classes.subMenu)[0];
      var subMenuTrigger = item.getElementsByClassName(classes.subMenuTrigger)[0];
      if (subMenuTrigger) subMenuTrigger.classList.remove(classes.active);

      if (subMenu) {
        subMenu.classList.remove(classes.active);
        DOMAnimations.slideUp(subMenu);
      }
    });
  }

  function offClick(e) {
    if (!menu.contains(e.target) && !e.target.classList.contains(classes.menuTrigger)) {
      document.removeEventListener('click', offClick, false);
      reset();
    }
  }
}

pushMenu();

function videoControls() {
  var videos = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  videos.forEach(function (video) {
    var parent = video.parentElement;
    var audioBtn = parent.getElementsByClassName('km-mb-audio-control')[0];
    var stateControlBtn = parent.getElementsByClassName('km-mb-state-control')[0];
    var fitParent = video.hasAttribute('fitparent');

    if (audioBtn) {
      var audioTarget = audioBtn.getElementsByClassName('km-icon')[0] || audioBtn;
      audioBtn.addEventListener('click', function () {
        audioTarget.classList.remove('km-icon-sound-on', 'km-icon-sound-off');

        if (video.muted === true) {
          video.muted = false;
          audioTarget.classList.add('km-icon-sound-on');
        } else if (video.muted === false) {
          video.muted = true;
          audioTarget.classList.add('km-icon-sound-off');
        }
      });
    }

    if (stateControlBtn) {
      var stateTarget = stateControlBtn.getElementsByClassName('km-icon')[0] || stateControlBtn;
      stateTarget.addEventListener('click', function () {
        if (fitParent) fitVideo(video.parentElement, video, true);
        stateTarget.classList.remove('km-icon-play', 'km-icon-pause');

        if (video.paused) {
          video.play();
          stateTarget.classList.add('km-icon-pause');
        } else {
          video.pause();
          stateTarget.classList.add('km-icon-play');
        }
      });
    }
  });
}