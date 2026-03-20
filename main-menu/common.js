(function initiateMenu() {
  addSubmenuIcons();
  pushMenu();

  function pushMenu() {
    const menu = document.getElementById('push-menu');
    const triggers = Array.prototype.slice.call(
      document.getElementsByClassName('mm-toggle')
    );

    triggers.forEach((trigger) => {
      trigger.addEventListener(
        'click',
        () => {
          trigger.classList.toggle('pm-trigger-active');
          document.documentElement.classList.toggle('push-menu-active');
          revealMenu(
            document.documentElement.classList.contains('push-menu-active')
          );
        },
        false
      );
    });

    if (!menu) return;
  }

  function addSubmenuIcons() {
    const menus = Array.prototype.slice.call(
      document.getElementsByClassName('main-nav')
    );

    menus.forEach((menu) => {
      let submenuTriggers = Array.prototype.slice.call(
        menu.getElementsByClassName('menu-item-has-children')
      );

      submenuTriggers.forEach((sub) => {
        const trigger = getIcon('#submenu-icon', 'sub-menu-trigger');
        const dropdown = sub.querySelector('ul');
        if (!dropdown) return;

        if (menu.classList.contains('push-menu')) {
          toggleItem(trigger, dropdown, 'sub-menu-active', () => {
            activateDropdown(sub);
          });
          sub.insertBefore(trigger, sub.firstChild.nextSibling);
        } else {
          sub.appendChild(trigger);
        }

        //sub.insertBefore(trigger, sub.firstChild);
      });
    });
  }

  function toggleItem(trigger, target, activeClass = 'active', cb = null) {
    const domElems = trigger instanceof Element && target instanceof Element;
    if (!domElems) throw new Error('Invalid arguments. Expected DOM Elements');

    trigger.addEventListener(
      'click',
      (e) => {
        e.preventDefault();
        document.documentElement.classList.toggle(activeClass);
        let hasClass = document.documentElement.classList.contains(activeClass);
        if (cb) cb(hasClass);
      },
      false
    );
  }

  function activateDropdown(sub) {
    const submenu = sub.querySelector('.sub-menu');
    sub.classList.toggle('active');
    DOMAnimations.slideToggle(submenu, 200);
  }

  function getIcon(id, className = 'sub-menu-trigger') {
    const wrap = document.createElement('div');
    wrap.className = className;
    const svgElem = document.createElementNS(
      'http://www.w3.org/2000/svg',
      'svg'
    );
    const useElem = document.createElementNS(
      'http://www.w3.org/2000/svg',
      'use'
    );
    useElem.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', id);
    svgElem.appendChild(useElem);
    wrap.appendChild(svgElem);
    return wrap;
  }

  // Reveal Menu
  function revealMenu(menuState) {
    const isActive = menuState || false;
    const stagClass = 'km-item-visible';
    const sectionRevealTime = 400; // matches with transiiton speed in CSS
    const menuItems = Array.prototype.slice.call(
      document.querySelectorAll('.push-menu>ul>li')
    );

    if (isActive) {
      setTimeout(() => {
        staggerClass(menuItems, stagClass);
      }, sectionRevealTime);
    } else {
      setTimeout(() => {
        menuItems.forEach((item) => item.classList.remove(stagClass));
      }, sectionRevealTime);
    }

    // Reveal after delay elements after menu
    function revealOnDelay(sel, delay) {
      const item = document.querySelector(sel);
      if (!item) return;

      if (menuState) {
        setTimeout(() => {
          item.classList.add('active');
        }, menuItems.length * 100 + delay); // stagger class time
      } else {
        item.classList.remove('active');
      }
    }

    // revealOnDelay('.main-nav .nav-phone', 400);
    revealOnDelay('.main-nav .km-search-form', 400);
    revealOnDelay('.push-menu-info', 800);
    
  }

  // Stagger appearance
  function staggerClass(items = [], className = 'active') {
    for (let i = 0; i < items.length; i++) {
      setTimeout(function timer() {
        items[i].classList.add(className);
      }, i * 100);
    }
  }

    // Close menu on ESC
    document.onkeydown = function (evt) {
      evt = evt || window.event;
      // console.log(evt.key === "Escape");
      if (evt.key === "Escape") {
        const target = document.documentElement;
        const menuActive = target.classList.contains('push-menu-active');
        if (menuActive) target.classList.remove('push-menu-active');
      }
    };

})();
