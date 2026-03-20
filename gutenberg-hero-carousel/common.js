
  function hero_carousel_init() {
    const heroWrappers = document.querySelectorAll('.km-hero-carousel');
    const carousels = Array.prototype.slice.call(heroWrappers);
    carousels.forEach((carousel) => {
      let scrollButton = carousel.getElementsByClassName(
        'km-carousel-scroll-down'
      )[0];

      if (scrollButton) scrollToElement('.km-carousel-scroll-down'); // scroll down button
      initSlideCarousel(carousel); // carousel core
    });

    function initSlideCarousel(carousel) {
      const slideCarousel = carousel.getElementsByClassName(
        'km-slide-carousel'
      )[0];
    
      if (!slideCarousel) return;
    
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
        on: {
          slideChange: function () {
            handleSlideChange(this.realIndex, carousel);
          },
          init: function () {
            handleSlideChange(this.realIndex, carousel);
          },
        },
        navigation: {},
        pagination: {
          clickable: true,
          renderBullet: function (index, className) {
            return (
              '<span class="carousel-bullet ' +
              className +
              '"><svg><use xlink:href="#swiper-square" /></svg></span>'
            );
          },
        },
      };
    
      // Assign conditional props
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
      new Swiper(slideCarousel, props);
    }
    
    function handleSlideChange(index, carousel) {
      const target = carousel.getElementsByClassName('km-content-carousel')[0];
      if (!target) return;
      const activeClass = 'km-carousel-content-active';
      const slides = Array.prototype.slice.call(
        target.getElementsByClassName('km-carousel-slide-content')
      );
    
      if (slides.length === 0) return;
    
      slides.forEach(function (slide) {
        let slideId = slide.dataset.slide || null;
        slide.classList.remove(activeClass);
        if (parseInt(slideId) === index) slide.classList.add(activeClass);
      });
    }
    
    // Video background
    kmIntersect(Array.from(document.querySelectorAll(".lazy-bg-video")), {
      observerProps: {
        threshold: [0.3] // 30% of element is visible
      },
      on: (item,observer) => loadLazyVideo(item,observer),
      off: (item,observer) => pauseLazyVideo(item,observer)
    });

  }

  hero_carousel_init();




