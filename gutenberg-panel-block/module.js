
function panel_block_init() {
  // Accordion will be initialized only if parent has id property
  const instances = Array.prototype.slice.call(
    document.querySelectorAll(".km-panel-block")
  );

  instances.forEach(function (inst, index) {
    let isAccordion = inst.dataset.activateAccordion === "1"; // data-activate-accordion
    let isMobile = window.matchMedia("(max-width: 980px)");
    let instance = null;
    inst.id = "pb-" + (index + 1);
    let initial = parseInt(inst.dataset.pbInitial);
    let props = {
      config: {
        // initialActive: 0,
        offClick: true
      },
      selectors: {
        trigger: "#" + inst.id + " .km-pb-trigger",
        content: "#" + inst.id + " .km-pb-panel-content"
      }
    };
    if (inst.dataset.pbOffclick)
      props.config.offClick =
        inst.dataset.pbOffclick === "true" ? true : false;
    if (inst.dataset.pbInitial)
      props.config.initialActive = initial === 0 ? 0 : initial;

    function controlSwiper(isMobile) {
      if (isMobile.matches && isAccordion) {
        instance = new KetchupAccordion(props);
      } else {
        if (instance) {
          instance.destroy();
          instance = null;
        }
      }
    }
    isMobile.addListener(controlSwiper);
    controlSwiper(isMobile);
  });
}
panel_block_init();
