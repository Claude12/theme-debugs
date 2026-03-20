// Use this files to call functions that are not module dependant

window.addEventListener('DOMContentLoaded', function () {

  // Initiate cookie manager if needed. Helps with cookie CRUD
  const kmCookieManager = new Cookie();

  // Check if user did not accept analytical cookies. Remove GTM cookies if not accepted.
  function gtmCheck(){
    const src = document.getElementById('km-gtm-cookie-ref');
    if(!src) return; // user is ok with analytical cookies if src is not rendered

    const cookiesToRemove = src.innerText.split(',');

    cookiesToRemove.forEach(cookie => {
      kmCookieManager.delete(cookie);
    });
  }

  gtmCheck();

  // Alters ninja form buttons by attribute
  jQuery(document).on('nfFormReady', function () {
    aniLabel();
  });

  // Grant ability to change swiper controls colour
  detectProp(
    '.km-has-swiper-colour', // selector
    '.km-nav-colour-prop', // element within selector
    'km-nav-colour', // data-attribute
    updateSwiperColours // logic for updating colours
  );

  kmIntersect(Array.from(document.querySelectorAll(".lazy-bg-image")), {
    observerProps: {
     threshold: [0.1] // 40% of element is visible
   },
    on: (item,observer) => lazyBgImage(item,observer)
  });

  kmIntersect(Array.from(document.querySelectorAll(".split-content-section")), {
    observerProps: {
     threshold: [0.2] 
   },
    on: (item,observer) => {
      item.target.classList.remove('scs-invisible');
      item.target.classList.add('scs-visible');
    },
    off: (item,observer) => {
      item.target.classList.add('scs-invisible');
      item.target.classList.remove('scs-visible');
    },
  });
});
