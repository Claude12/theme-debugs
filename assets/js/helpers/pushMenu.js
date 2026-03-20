function pushMenu(){
  const classes = {
    menu: 'push-menu',
    hasChildren: 'menu-item-has-children',
    active: 'active',
    menuTrigger: 'push-menu-trigger',
    subMenu: 'sub-menu',
    subMenuTrigger:'sub-menu-trigger'
  }
  
  const html = document.documentElement;
  const menu = document.getElementsByClassName(classes.menu)[0];
  const menuTriggers = Array.from(document.getElementsByClassName(classes.menuTrigger));
  const items = Array.from(document.getElementsByClassName(classes.hasChildren));

  let triggerElement = () => {
    const trigger = document.createElement('button');
    const icon = document.createElement('span');
    icon.className= 'km-icon-arrow-right';
    trigger.appendChild(icon);
    trigger.className = classes.subMenuTrigger;
    
    trigger.addEventListener('click', (e) => {
      let ele = e.currentTarget;
      let target = ele.nextElementSibling;
      if(target.classList.contains(classes.subMenu)) {
        ele.classList.toggle(classes.active);
        target.classList.toggle(classes.active);
        DOMAnimations.slideToggle(target);
      }
    },false);
    
    return trigger;
  }

  items.map(item => {
    
    // Add sub-menu class
    let dropdown = item.querySelector('ul');
    if(!dropdown) return;
    dropdown.classList.add(classes.subMenu);
    
    // Add sub menu trigger
    item.insertBefore(triggerElement(), item.firstChild.nextSibling);
  });
  
  // Set main menu triggers
  menuTriggers.forEach(trigger => {
    trigger.addEventListener('click',(e) => {
      html.classList.toggle('push-menu-active');
      document.addEventListener('click', offClick, false);
      let isActive = html.classList.contains('push-menu-active');
      if(isActive){
        window.addEventListener('resize', reset, false);
        
      } else {
        reset();
         window.removeEventListener('resize', reset, false);
      }
    }, false);
  }); 
  
  
  function reset(){
    html.classList.remove('push-menu-active');
    items.forEach(item => {
      let subMenu = item.getElementsByClassName(classes.subMenu)[0];
      let subMenuTrigger = item.getElementsByClassName(classes.subMenuTrigger)[0];
      if(subMenuTrigger)subMenuTrigger.classList.remove(classes.active);

      if(subMenu){
        subMenu.classList.remove(classes.active);
        DOMAnimations.slideUp(subMenu);
      }
    });

  }
  
  function offClick(e){
  
    if(!menu.contains(e.target) && !e.target.classList.contains(classes.menuTrigger)) {
      document.removeEventListener('click', offClick, false);
      reset();
    }
  }
  
}

pushMenu();
