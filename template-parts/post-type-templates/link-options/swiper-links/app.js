// var mySwiper = new Swiper('.swiper-container', {
//   speed: 400,
//   spaceBetween: 100
// });

window.addEventListener('DOMContentLoaded', () => initCPTLinks());

function initCPTLinks() {
  const sel = '.km-cpt-links';
  const cptLinks = document.querySelector(sel + ' .swiper-container');

  if (!cptLinks) return;

  var mySwiper = new Swiper(cptLinks, {
    speed: 400,
    spaceBetween: 7,
    slidesPerView: 4.4,
    breakpoints: {
      1201: {
        slidesPerView: 4.4,
      },
      980: {
        slidesPerView: 3.4,
      },
      640: {
        slidesPerView: 2.4,
      },
      320: {
        slidesPerView: 1,
      },
    },
    navigation: {
      nextEl: sel + ' .swiper-button-next',
      prevEl: sel + ' .swiper-button-prev',
    },
    scrollbar: {
      el: sel + ' .swiper-scrollbar',
      draggable: true,
    },
  });

}
