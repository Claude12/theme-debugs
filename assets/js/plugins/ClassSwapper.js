class ClassSwapper {
  constructor(userProps = {}) {
    this.defaults = {
      media: '(max-width:980px)', // any valid CSS media query
      elements: [], // Array of DOM objects.
      element: null, // DOM object
      match: [], // class names when media is matched
      noMatch: [], // class names when media is not matched
    };

    this.props = this.mergeDeep(this.defaults, userProps);
    this.media = window.matchMedia(this.props.media);

    // Bind Methods
    this.init = this.init.bind(this);
    this.mediaControl = this.mediaControl.bind(this);
    this.reset = this.reset.bind(this);

    this.init();
  }

  init() {
    if (this.props.element) this.props.elements.push(this.props.element);
    this.listener = this.media.addListener(this.mediaControl);
    this.mediaControl(this.media);
  }

  mediaControl(media) {
    this.reset();
    this.classAction(media.matches);
  }

  reset() {
    const classNames = [...this.props.match, ...this.props.noMatch];
    this.props.elements.forEach(function (element) {
      classNames.forEach(function (className) {
        element.classList.remove(className);
      });
    });
  }

  classAction(mediaMatch) {
    const targetClasses = mediaMatch ? this.props.match : this.props.noMatch;
    this.props.elements.forEach(function (element) {
      targetClasses.forEach(function (className) {
        element.classList.add(className);
      });
    });
  }

  mergeDeep(target, source) {
    function isObject(item) {
      return (
        item &&
        typeof item === 'object' &&
        !Array.isArray(item) &&
        item !== null
      );
    }

    if (isObject(target) && isObject(source)) {
      Object.keys(source).forEach((key) => {
        if (isObject(source[key])) {
          if (!target[key] || !isObject(target[key])) {
            target[key] = source[key];
          }
          this.mergeDeep(target[key], source[key]);
        } else {
          Object.assign(target, { [key]: source[key] });
        }
      });
    }
    return target;
  }
}

// DEMOS

// const menu = new ClassSwapper({
//   elements: [...document.getElementsByClassName('main-nav')],
//   match: ['has-teal-background'],
//   noMatch: ['has-black-bg']
// });

// const menuText = new ClassSwapper({
//   elements: Array.prototype.slice.call(document.querySelectorAll('.menu-text')),
//   match: ['has-black-colour'],
//   noMatch: ['has-white-colour']
// });

// const box = new ClassSwapper({
//   media: '(min-width:981px) and (max-width:1200px)',
//   elements: Array.from(document.getElementsByClassName('box')),
//   match: ['box-red'],
//   noMatch: ['box-salmon']
// });

// const circle = new ClassSwapper({
//   media: '(min-width:981px)',
//   element: document.getElementById('circle'), // elements must be array. element can be DOM object
//   match: ['circle-coral'],
//   noMatch: ['circle-pink']
// });
