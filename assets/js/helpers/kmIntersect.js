/**
* Function allows us to intersect moment when element appears within view.
* 
* kmIntersect(Array.from(document.querySelectorAll(".lazy-video")), {
*    observerProps: {
*      threshold: [0.9] // 90% of element is visible
*    },
*    on: (item,observer) => loadVideo(item,observer), // optional
*    off: (item,observer) => pauseVideo(item,observer) // Optional
*  });
*
**/
function kmIntersect(items = [], userProps = { observerProps: {} }) {
  if (!("IntersectionObserver" in window)) return;
  const defaultProps = {
    observerProps: {
      threshold: [0.5] // 50% of element is in viewport
    },
    on: undefined,
    off: undefined
  };

  const props = { ...defaultProps, ...userProps };
  const observerProps = {
    ...defaultProps.observerProps,
    ...userProps.observerProps
  };
  const observerInst = new IntersectionObserver(observe, observerProps);

  function observe(items, observer) {
    items.forEach((item) => {
      if(item.isIntersecting){
        if(props.on) props.on(item, observerInst);
  
      }else {
        if(props.off)props.off(item, observerInst);
      }
    });
  }

  items.forEach((item) => observerInst.observe(item));
}