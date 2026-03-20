class CategorySorter {
  constructor(userProps = {}, clickCb = null) {
    this.defaults = {
      parent: null, // DOM element. parent holding cards
      categories: [], // DOM array of elements that contain categoryIdentifier
      cards: [], // DOM array of cards to be sorted
      categoryIdentifier: 'categoryId', //data attribute containing list of matching categories
      initialCats: [], // category identifiers to enable on page load.
      enableAllCatsInitially: false, // if enabled - it will show all available posts. Useful for development
      cardSelector: null, // class name of single cards.
      allowMultipleCats: false, // enables ability to select multiple categories at the same time.
      classes: {
        selectedCat: 'cat-selected', // applied to category element (trigger)
        activeCard: 'card-active', // applied to active card ( if matches category)
      },
    };

    this.props = this.mergeDeep(this.defaults, userProps);
    this.clickCb = clickCb;
    this.selectedCategories = [];
    this.activeCards = [];
    this.init();
  }

  init() {
    const self = this;
    const buttons = this.props.categories;
    buttons.forEach((btn) => {
      btn.addEventListener('click', self.selectCategory.bind(this), false);
    });

    this.sortCards();
    this.updateInitialCats();
  }

  updateInitialCats() {
    const categories = this.props.categories;
    const initialCats = this.props.initialCats;

    categories.forEach((cat) => {
      let catId = cat.dataset[this.props.categoryIdentifier];
      if (
        initialCats.indexOf(catId) !== -1 ||
        this.props.enableAllCatsInitially
      )
        this.selectCategory(null, cat);
    });
  }

  selectCategory(e, ele) {
    let element;

    if (e) {
      e.preventDefault();
      element = e.currentTarget;
    } else {
      element = ele;
    }
    const selectedClass = this.props.classes.selectedCat;
    const id = element.dataset[this.props.categoryIdentifier];
    const cats = this.props.allowMultipleCats ? this.selectedCategories : [];
    const catTriggers = this.props.categories || [];

    if (!this.props.allowMultipleCats) {
      catTriggers.forEach((trigger) => {
        if (trigger !== element) trigger.classList.remove(selectedClass);
      });
    }

    element.classList.toggle(selectedClass);

    if (element.classList.contains(selectedClass) && cats.indexOf(id) === -1) {
      cats.push(id);
    } else {
      if (cats.indexOf(id) !== -1) cats.splice(cats.indexOf(id), 1);
    }

    this.selectedCategories = cats;

    if (this.clickCb) this.clickCb(this.categories, this.cards);
    this.sortCards();
  }

  sortCards() {
    const self = this;
    const cards = this.props.cards;
    const result = this.selectedCategories;

    // Clear results
    this.activeCards = [];

    cards.forEach((card) => {
      let categories = card.dataset.categories.split(',');

      categories.forEach((cat) => {
        if (
          result.indexOf(cat) !== -1 &&
          self.activeCards.indexOf(card) === -1
        ) {
          self.activeCards.push(card);
        }
      });
    });

    this.updateCards();

    // if no buttons are clicked initiate categories that were visible on page load
    if (!result.length) {
      this.updateInitialCats();
      this.updateCards();
    }
  }

  refreshCards() {
    const parent = this.props.parent || document;
    const self = this;
    const selector = this.props.cardSelector;
    if (!selector) return;
    const newCards = Array.prototype.slice.call(
      parent.querySelectorAll(this.props.cardSelector)
    );
    self.props.cards = newCards;
    self.sortCards();
    return self;
  }

  updateCards() {
    const props = this.props;
    const classes = props.classes;
    const allCards = this.props.cards;
    const activeCards = this.activeCards;

    // Remove all classes
    const resetCards = () => {
      allCards.forEach((item) => item.classList.remove(classes.activeCard));
      activateCards();
    };

    // Activate cards
    const activateCards = () => {
      activeCards.forEach((item) => item.classList.add(classes.activeCard));
    };

    resetCards();
  }

  // Getter
  get refresh() {
    return this.refreshCards();
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
