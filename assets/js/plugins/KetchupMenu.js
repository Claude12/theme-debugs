const KetchupMenu = function (opts, callback) {
  if (!(this instanceof KetchupMenu)) return new KetchupMenu(opts, callback);

  const defaults = {
    hasSubmenu: {
      activeClass: 'sub-menu-active',
      className: 'sub-menu-trigger',
      icon: '#chevron', // pass null to skip icon generation
      onClick: function (e) {
        const targetIndex = parseInt(e.currentTarget.dataset.submenu);
        this.toggleSubmenu(targetIndex);
      },
      initialVisible: null, // null default -none visible from the get go, alternatively provide index starting from 0
      injectLocation: 'start', // start- adds as first child to li and end adds as last child
    },
    offClick: {
      enabled: true,
      element: null, // if null it will check agains selectors.nav
    },
    selectors: {
      menuTypeHolder: 'html', // or null then call will be added to selectors.nav
      nav: '.main-nav',
      responsiveClass: 'push-menu',
      desktopClass: 'bar-menu',
      hasSubmenu: '.menu-item-has-children',
      submenu: 'ul',
    },
    menuTriggers: {
      selector: '.menu-trigger',
      activeClass: null, // null to disable - default or string to add a class
      onClick: this.toggleMenu,
    },
    menuActive: {
      element: 'html',
      activeClass: 'menu-active',
    },
    burgerWhen: '(max-width: 980px)',
  };

  const userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.mediaCheck = window.matchMedia(this.props.burgerWhen);
  this.isResponsive = false;
  this.menu = document.querySelector(this.props.selectors.nav);
  if (!this.menu)
    throw new Error(
      'nav cannot be found. Please check nav within selectors options'
    );
  this.submenus = Array.prototype.slice.call(
    this.menu.querySelectorAll(this.props.selectors.hasSubmenu)
  );

  this.submenuTriggers = [];
  this.navTriggers = [];
  return this;
};

KetchupMenu.prototype.initMainTriggers = function () {
  const self = this;
  const triggers = this.props.menuTriggers.selector;
  const elems = Array.prototype.slice.call(document.querySelectorAll(triggers));
  this.navTriggers = elems;

  elems.map(function (trigger) {
    trigger.addEventListener('click', self.toggleMenu.bind(self), false);
  });
};

KetchupMenu.prototype.toggleClass = function (element, className) {
  if (element.classList.contains(className)) {
    element.classList.remove(className);
  } else {
    element.classList.add(className);
  }
};

KetchupMenu.prototype.toggleMenu = function () {
  const self = this;
  const activeProps = this.props.menuActive;
  const triggerProps = this.props.menuTriggers;
  const target = document.querySelector(activeProps.element);

  // Control Main class depending on menu state
  this.toggleClass(target, activeProps.activeClass);

  // Add specific active class to triggers if needed
  if (triggerProps.activeClass !== null) {
    this.navTriggers.map(function (trigger) {
      self.toggleClass(trigger, triggerProps.activeClass);
    });
  }

  if (this.callback)
    this.callback({
      isResponsive: this.isResponsive,
      state: target.classList.contains(activeProps.activeClass)
        ? 'visible'
        : 'hidden',
    });
};

KetchupMenu.prototype.resetMenu = function () {
  const self = this;
  const activeProps = this.props.menuActive;
  const triggerProps = this.props.menuTriggers;
  const target = document.querySelector(activeProps.element);

  target.classList.remove(activeProps.activeClass);

  if (triggerProps.activeClass !== null) {
    this.navTriggers.map(function (trigger) {
      trigger.classList.remove(triggerProps.activeClass);
    });
  }

  this.submenus.map(function (submenu) {
    let submenuList = submenu.querySelector(self.props.selectors.submenu);
    if (submenuList) {
      submenu.classList.remove(self.props.hasSubmenu.activeClass);
      submenuList.removeAttribute('style');
    }
  });
};

KetchupMenu.prototype.initOffclick = function () {
  const self = this;
  const props = this.props;
  const target =
    props.offClick.element === null
      ? props.selectors.nav
      : props.offClick.element;
  const targetEle = document.querySelector(target);

  document.addEventListener('click', function (event) {
    let clickedTrigger = false;
    self.navTriggers.map(function (trigger) {
      if (trigger.contains(event.target)) {
        clickedTrigger = true;
      }
    });

    if (!targetEle.contains(event.target) && clickedTrigger === false) {
      self.resetMenu();
    }
  });
};

KetchupMenu.prototype.toggleSubmenu = function (targetIndex) {
  const self = this;
  const activeClass = self.props.hasSubmenu.activeClass;

  this.submenus.map(function (submenu, index) {
    let submenuList = submenu.querySelector(self.props.selectors.submenu);
    let submenuHeight = submenuList.scrollHeight;
    if (index === targetIndex) {
      if (submenu.classList.contains(activeClass)) {
        submenu.classList.remove(activeClass);
        submenuList.style.height = 0;
      } else {
        submenu.classList.add(activeClass);
        submenuList.style.height = submenuHeight + 'px';
      }
    } else {
      submenu.classList.remove(activeClass);
      submenuList.style.height = 0;
    }
  });
};

KetchupMenu.prototype.init = function (string) {
  const self = this;
  const quickBreakpoint = string || null;

  // Initialize quick breakpoint for burger menu
  if (quickBreakpoint !== null) {
    if (string.trim() === 'global') {
      self.mediaCheck = window.matchMedia('(min-width: 100px)');
    } else {
      self.mediaCheck = window.matchMedia(
        '(max-width: ' + quickBreakpoint + ')'
      );
    }
  }

  // Detect current view and listen to changes
  this.mediaCheck.addListener(self.detectView.bind(self));
  this.detectView.call(self, this.mediaCheck);

  // Inject icon if needed
  if (this.props.hasSubmenu.icon !== null) {
    this.createTrigger();
  }

  // Initialize main menu triggers
  this.initMainTriggers();

  // Initialize off click effect
  if (this.props.offClick.enabled === true) {
    this.initOffclick();
  }

  // Make initial menu item visible is set to true
  const initialVisible = this.props.hasSubmenu.initialVisible;
  if (this.isResponsive === true && initialVisible !== null) {
    this.toggleSubmenu(initialVisible);
  }

  return this;
};

KetchupMenu.prototype.detectView = function (query) {
  if (query.matches) {
    this.isResponsive = true;
  } else {
    this.isResponsive = false;
  }
  // Initiate menu class adjustments
  this.configMenu(this.isResponsive);

  return this;
};

KetchupMenu.prototype.configMenu = function (isResponsive) {
  const selectors = this.props.selectors;

  // Switch main class
  if (isResponsive === true) {
    this.switchClasses(selectors.desktopClass, selectors.responsiveClass);
  } else if (isResponsive === false) {
    this.switchClasses(selectors.responsiveClass, selectors.desktopClass);
    this.resetMenu();
  }
};

KetchupMenu.prototype.switchClasses = function (removeClass, addClass) {
  const typeHolder = this.props.selectors.menuTypeHolder;
  const target =
    typeHolder !== null ? document.querySelector(typeHolder) : this.menu;
  target.classList.remove(removeClass);
  target.classList.add(addClass);
  return this;
};

KetchupMenu.prototype.mergeObj = function () {
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

KetchupMenu.prototype.createTrigger = function () {
  const self = this;
  const submenus = this.submenus;
  const submenuConfig = this.props.hasSubmenu;
  const icon = submenuConfig.icon;
  const triggerClass = submenuConfig.className;

  if (submenus.length === 0) return this;

  submenus.map(function (item, index) {
    let iconWrap = document.createElement('span');
    iconWrap.classList.add(triggerClass);
    iconWrap.dataset.submenu = index;
    let svgElement = document.createElementNS(
      'http://www.w3.org/2000/svg',
      'svg'
    );
    let useElem = document.createElementNS('http://www.w3.org/2000/svg', 'use');
    useElem.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', icon);
    svgElement.appendChild(useElem);
    iconWrap.appendChild(svgElement);
    iconWrap.addEventListener(
      'click',
      self.props.hasSubmenu.onClick.bind(self),
      false
    );
    self.submenuTriggers.push(iconWrap);

    if (submenuConfig.injectLocation === 'start') {
      item.insertBefore(iconWrap, item.childNodes[0]);
    } else {
      item.appendChild(iconWrap);
    }
  });
};
