/**
 *  Animate icons
 * 
 */

function gsap_animateIcons(){

  const blocks = [...document.querySelectorAll('.km-icons-block')];

  blocks.forEach(block => {
    let title = block.querySelector('.km-icb-main-title');
    let iconTitle = block.querySelector('.km-icb-icon-title');
    let content = block.querySelector('.km-icb-content-wrap');
    let icons = [...block.querySelectorAll('.km-icb-item')]; //.km-icb-icon

    let tl = gsap.timeline({
      scrollTrigger: {
        trigger: block,
        // scrub:1,
        start: "top 90%", // trigger, scroll section
        //markers: true,
        toggleActions: 'restart none resume reverse'
      }
    });

    if(title) tl.from(title, { opacity:0})
    if(content) tl.from(content, {opacity:0}, "+=0.2")
    if(iconTitle) tl.from(iconTitle, {opacity:0}, "+=0.2")
    if(icons.length > 0) {
      tl.from(icons, {
        opacity:0,
        scale: 0.1,
        duration:0.3,
        stagger:'0.2'
      });
    }

  });

}


/**
 * 
 * @param {String} block container block
 * @param {String} items scale in element
 *  gsap_scaleIn('.km-logo-collection','.km-lcl-item-wrap');
 */
function gsap_scaleIn(block,items){
  const blocks = [...document.querySelectorAll(block)];
  blocks.forEach(block => {
    let items = [...block.querySelectorAll(items)];

    if (items.length > 0) {
      gsap.from(items, {
        scrollTrigger: {
          trigger: block,
          start: "30% center",
          toggleActions: 'restart none resume none'
        },
        opacity:0,
        scale: 0.1,
        duration:0.3,
        stagger:'0.2'
      });
    }
  });

};

/**
 * 
 * @param {String} block container block
 * @param {STring} imageSel parallax item
 */
function gsap_Parallax(block, imageSel){

  const blocks = [...document.querySelectorAll(block)];
  blocks.forEach(block => {

  let image = block.querySelector(imageSel);
    gsap.to(image, {
      yPercent: 20,
      ease: "none",
      scrollTrigger: {
        trigger: block,
        // start: "top bottom", // the default values
        // end: "bottom top",
        scrub: true
      }, 
    });

  });
}

/**
 * 
 * @param {String} block Parent container
 * @param {String} item element
 *   gsap_enterPos('.km-alternating-content-block','.km-acb-a');
 */
function gsap_enterPos(block,item) {
  const blocks = [...document.querySelectorAll(block)];
  blocks.forEach(block => {
    let ele = block.querySelector(item);
    let isRight = block.classList.contains('image-location-right') ? true : false;
    let isContent = item === '.km-acb-b' ? true : false;

    const props = {
      opacity: 0,
      ease: "power3",
      duration:1,
      scrollTrigger: {
        trigger: block,
        start: '-=5% center', // when elements top reaches middle of the scroller // area you scroll in. could be pixels or percents top: from element, bottom from viewport
        end:  '+=10%', //default when bottom of the element hits bottom of the trigger relative "+=300" : end +300px after start
        //markers:true,
        toggleActions: 'play none none reverse' // start ,forward past end point, when back in view, all the way back
      }, 
    };

     props.xPercent = isRight ? isContent ? -20 : 20 :  isContent ? 20 : -20;

    gsap.from(ele, props);

  });
}

/**
 * 
 * @param {String} block Element to slide in
 * gsap_watermark('.km-watermark');
 */
function gsap_watermark(block) {
  const blocks = [...document.querySelectorAll(block)];

  blocks.forEach(block => {
    const props = {
      scrollTrigger: {
        trigger: block,
        start: 'top center', // when elements top reaches middle of the scroller // area you scroll in. could be pixels or percents top: from element, bottom from viewport
        end:  '+=100', //default when bottom of the element hits bottom of the trigger relative "+=300" : end +300px after start
        //markers:true,
        onEnter: () => block.classList.add('km-ani-on')
      }, 
    };
    gsap.from(block, props);

  });
}