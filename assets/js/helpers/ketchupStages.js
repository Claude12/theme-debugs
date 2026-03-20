function ketchupStages(obj) {
  const arr = obj.stages || [];
  const selector = obj.selector || null;
  const prefix = obj.classPrefix || 'stage';
  const dirUp = obj.dirUp || 'up';
  const asAttr = obj.stageAsAttribute === true ? true : false;
  const dirDown = obj.dirDown || 'down';
  const setScrollDirection = obj.setScrollDirection || false;
  const elements = Array.prototype.slice.call(document.querySelectorAll(selector));
  if (elements.length === 0) return;
  const supportOffset = window.pageYOffset !== undefined;
  let lastKnownPos = 0;
  let ticking = 0;
  let scrollDir = obj.dirLoad || 'load';

  window.addEventListener('scroll', function(e) {
    let currYPos = supportOffset ? window.pageYOffset : document.body.scrollTop;
    scrollDir = lastKnownPos > currYPos ? dirUp : dirDown;
    lastKnownPos = currYPos;

    if (!ticking) {
      window.requestAnimationFrame(function() {
        action(lastKnownPos, scrollDir);
        ticking = false;
      });
    }
    ticking = true;
  });

  function action(scrollPos, scrollDir) {
    arr.forEach(function(item, index) {
      let values = item.split(',');

      if (setScrollDirection === true) {
        elements.forEach(function(item) {
          item.dataset.direction = scrollDir;
        });
      }

      if (scrollPos >= values[0] && scrollPos <= values[1]) {
        elements.forEach(function(item) {
          if (asAttr) {
            item.dataset.stage = prefix + (index + 1);
          } else {
            item.className = prefix + (index + 1);
          }
        });
      }
    });
  }
  // Run on page load
  action(lastKnownPos, scrollDir);
}

// ketchupStages({
//   selector: "#header", // selectors to perform stages on
//   stages: ["100,200", "200,300", "300,400"], // stages (from 100 to 200 px : stage 1 etc). Controlls by index + 1. default []
//   stageAsAttribute: false, // if true instead of adding class names data.stage will be added. useful if your selector has more than one class!
//   classPrefix: 'stage-', // active stage elements get class of prefix + (index + 1)
//   setScrollDirection: true, // add data.direction attribute or not. this will say load if no scroll detected, up or down
//   dirUp: 'up', // change data.direction to up when user scrolls back up
//   dirDown: 'down', // change data.direction to down when user scrolls down
//   dirLoad: 'load'// page load
// });

//commented out opts are optional.

// What happens if element( #header ) not found on a page - nothing - function returns
// limitation to stages - none
// will selector select all instances of given selector? - yes
// limitations - elements cannot have multiple classes if stageAsAttribute is set to false (default).

// Usual usage
// ketchupStages({
//   selector: "#header", // selector that gets stage effect
//   stages: ["100,200", "200,300", "500,600"], // range of stages in pixels
// });
