
  kmIntersect(Array.from(document.querySelectorAll(".lazy-bg-video")), {
    observerProps: {
      threshold: [0.3] // 30% of element is visible
    },
    on: (item,observer) => loadLazyVideo(item,observer),
    off: (item,observer) => pauseLazyVideo(item,observer)
  });
  
  
  const kmCarousels = Array.from(document.getElementsByClassName('km-carousel'));
  kmCarousels.forEach(carousel => kmCarousel(carousel));

  function kmCarousel(carousel = null){
  
    if(!carousel) return;
  
    const prevBtn = carousel.getElementsByClassName('swiper-button-prev')[0];
    const nextBtn = carousel.getElementsByClassName('swiper-button-next')[0];
    const pagination = carousel.getElementsByClassName('swiper-pagination')[0];
    const dataAttr = carousel.dataset;
    const paginationProp = dataAttr.pagination || null;
    const sideArrows = dataAttr.arrows === '1' ? true : false;
  
    const props = {
      speed: dataAttr.speed ? parseInt(dataAttr.speed) : 300,
      spaceBetween: 100,
      loop: dataAttr.loop === '1' ? true : false,
      effect: 'fade',
      fadeEffect: {
        crossFade: true,
      },
      navigation: {},
      pagination: {
        clickable: true,
        renderBullet: (index, className) => { return `<button class="carousel-bullet ${className}"></button>`}
        },
    };
  
    if (sideArrows && prevBtn) props.navigation.prevEl = prevBtn;
    if (sideArrows && nextBtn) props.navigation.nextEl = nextBtn;
    if (pagination) props.pagination.el = pagination;
    if (paginationProp) props.pagination.type = dataAttr.pagination;
    if (dataAttr.autoplay) {
      props.autoplay = {};
      props.autoplay.delay = dataAttr.autoplay;
    }
  
    if (paginationProp === 'bullets') {
      props.renderBullet = function (index, className) {
        return '<span class="' + className + '">' + (index + 1) + '</span>';
      };
    }
    new Swiper(carousel.getElementsByClassName('swiper-container')[0], props);
  
  
    if(carousel.getElementsByClassName('km-carousel-scroll')[0]) scrollToElement('.km-carousel-scroll');
  }