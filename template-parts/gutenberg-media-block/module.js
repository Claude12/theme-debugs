  
  // Video
  videoControls(Array.from(document.getElementsByClassName('km-mb-video')));
  
  // Gallery
  const kmMediaGalleries = Array.from(document.getElementsByClassName('km-mb-gallery'));
  kmMediaGalleries.forEach(gallery => kmCarousel(gallery));

  function kmCarousel(gallery = null){
  
    if(!gallery) return;
  
    const prevBtn = gallery.getElementsByClassName('km-mb-prev')[0];
    const nextBtn = gallery.getElementsByClassName('km-mb-next')[0];
    const pagination = gallery.getElementsByClassName('swiper-pagination')[0];
    const dataAttr = gallery.dataset;
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
        renderBullet: (index, className) => { return `<button class="${className}"><span class="km-icon km-icon-dot">&nbsp;</span></button>`}
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
  
    new Swiper(gallery.getElementsByClassName('swiper-container')[0], props);
  }


  // If content animation is enabled
  kmIntersect(Array.from(document.getElementsByClassName('km-mb-animated')), {
     observerProps: {
       threshold: [0.5] // 20% of element is visible
     },
     on: (item,observer) => {
      item.target.classList.remove('animation-inactive');
      item.target.classList.add('animation-active');
     },
     off: (item,observer) => {
      item.target.classList.add('animation-inactive');
      item.target.classList.remove('animation-active');
     }
   });
