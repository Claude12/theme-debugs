function scrollToElement(btn, speed, offset) {
  const topOffset = offset || 0;
  const aniSpeed = speed || 500;

  jQuery(btn).click(function () {
    if (
      location.pathname.replace(/^\//, '') ==
        this.pathname.replace(/^\//, '') ||
      location.hostname == this.hostname
    ) {
      let target = jQuery(this.hash);
      target = target.length
        ? target
        : jQuery('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        jQuery('html,body').animate(
          {
            scrollTop: target.offset().top - topOffset,
          },
          aniSpeed
        );
        return false;
      }
    }
  });
}

// scrollToElement(selector,animation speed milliseconds, offset top);
