window.addEventListener('DOMContentLoaded', function () {
  if (window.acf) {
    window.acf.addAction(
      'render_block_preview',
      quotes_block_1594725069774_init
    );
    window.acf.addAction('remount', quotes_block_1594725069774_init);
  } else {
    quotes_block_1594725069774_init();
  }

  function quotes_block_1594725069774_init() {
    // Props set here will override anything set inside data atrributes of element.
    const props = {
      cap: 1,
      slidesPerViewBreakpoint: 1200, // this breakpoint will be added to swiper breakpoints with WP admin value slides per view
      enableMedia: '(min-width: 100px)',
      swiperProps: {
        effect: 'fade',
        fadeEffect: {
          crossFade: true,
        },
        spaceBetween: 0,
        breakpoints: {
          320: {
            slidesPerView: 1,
          },
        },
      },
    };

    initSwiper('.km-testimonial-block', props);

    detectProp(
      '.km-testimonial-block',
      '.km-nav-colour-prop',
      'km-nav-colour',
      updateSwiperColours
    );

    function updateSwiperColours(element, slug) {
      addSlug(element, '.swn-icon svg', ['has-' + slug + '-fill']);
      addSlug(element, '.swiper-pagination-bullet', [
        'has-' + slug + '-border-colour',
      ]);
      addSlug(element, '.swiper-pagination-bullet svg', [
        'has-' + slug + '-fill',
      ]);

      addSlug(element, '.fn-fraction', ['has-' + slug + '-colour']);
    }
  }
});
