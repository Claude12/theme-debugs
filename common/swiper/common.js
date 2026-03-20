function initSwiper(selector, config) {
  const props = config || {};
  const currentInstances = [];

  // destroy and re init
  currentInstances.forEach(function (inst) {
    inst.destroy(true, true);
  });

  const blocks = Array.prototype.slice.call(
    document.querySelectorAll(selector)
  );


  blocks.forEach(function (block, index) {
    if (!block.querySelector('.swiper-container')) return;

    let swiperId =
      'km-' + getAbbr(selector.split('.km-')[1]) + '-' + (index + 1);
    block.id = swiperId;
    let navType = 'nav' in block.dataset ? block.dataset['nav'] : 'bullets';

    let defaults = {
      targetSwiper: '#' + swiperId + ' .swiper-container',
      classHolder: '#' + swiperId,
      classPrefix: 'swiper-is-',
      swiperProps: {
        navigation: {
          nextEl: '#' + swiperId + ' .next-btn',
          prevEl: '#' + swiperId + ' .prev-btn',
        },
        pagination: {
          el: '#' + swiperId + ' .fn-' + navType,
          type: navType,
          clickable: true,
          renderBullet: function (index, className) {
            return (
              '<span class="swn-bullet ' +
              className +
              '"><svg><use xlink:href="#swiper-square" /></svg></span>'
            );
          },
        },
      },
    };

    let swiperConfig = mergeObj(true, defaults, props);
    swiperConfig.swiperProps = mergeObj(
      true,
      swiperConfig.swiperProps,
      propsFromAttributes(block)
    );

    let instance = new KetchupSwiper(swiperConfig);
    instance.init();
    currentInstances.push(instance);

    // console.log(currentInstances);
    //console.log(instance);
    //console.log(swiperConfig);
  });

  function getAbbr(string) {
    const arr = string.split('-');
    const result = arr.map(function (item) {
      return item.split('')[0];
    });

    return result.join('');
  }

  function mergeObj() {
    // Variables
    var extended = {};
    var deep = false;
    var i = 0;
    var length = arguments.length;

    // Check if a deep merge
    if (Object.prototype.toString.call(arguments[0]) === '[object Boolean]') {
      deep = arguments[0];
      i++;
    }

    // Merge the object into the extended object
    var merge = function (obj) {
      for (var prop in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, prop)) {
          // If deep merge and property is an object, merge properties
          if (
            deep &&
            Object.prototype.toString.call(obj[prop]) === '[object Object]'
          ) {
            extended[prop] = mergeObj(true, extended[prop], obj[prop]);
          } else {
            extended[prop] = obj[prop];
          }
        }
      }
    };

    // Loop through each object and conduct a merge
    for (i = 0; i < length; i++) {
      var obj = arguments[i];
      merge(obj);
    }

    return extended;
  }

  function propsFromAttributes(element) {
    const source = element.dataset;
    const result = {};

    Object.keys(source).forEach(function (key) {
      switch (key) {
        case 'autoplay':
          const interval =
            'slideInterval' in source ? parseInt(source.slideInterval) : 3000;
          if (source.autoplay === 'true') {
            result.autoplay = {};
            result.autoplay.delay = interval;
          }
          break;
        case 'speed':
          result.speed = parseFloat(source.speed);
          break;
        case 'autoHeight':
          result.autoHeight = source.autoHeight === 'true' ? true : false;
          break;
        case 'slidesPerView':
          result.slidesPerView = parseFloat(source.slidesPerView);
          if (props.slidesPerViewBreakpoint) {
            result.breakpoints = {};
            result.breakpoints[props.slidesPerViewBreakpoint] = {
              slidesPerView: parseFloat(source.slidesPerView),
            };
          }
          break;
        case 'loop':
          result.loop = source.loop === 'true' ? true : false;
          break;
        default:
          break;
      }
    });

    return result;
  }
}
