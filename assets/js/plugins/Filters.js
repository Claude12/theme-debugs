class Filters {
  constructor(userProps, callback) {
    this.userProps = userProps || {};
    this.defaults = {
      config: {
        section: 'body', // where to append filter bar
        location: 'top', // or 'bottom'
        items: null, // items to be sorted
        activeClass: 'active', // active class for sorted items
        valueForAllItems: 'all', // value to be set to return all items within filter.
        populateInfoBar: true, // show info bar or not?
        infoIntro: null, // string for info bar introduction
        countFilterBars: false,
        fetchFilterFromHTML: null, // populate categories data from HTML
        clearFiltersfunct: true, // if true - clear all button will be created
        noResultsClass: 'km-filter-no-results', // pass in null if you dont want this to happen
        itemDelimiter: ' -|- ', // add this to data attribute if element belongs to multiple categories. <div data-service="option one -|- Option Two -|- Option Three">Product 1</div>
        clearFilters: {
          tag: 'button',
          classes: 'km-clear-filter',
          text: 'Clear filters',
          appendTo: null, // null will mean filter bar iself
        },
      },
      html: {
        filterBar: {
          class: 'filter-bar',
          tag: 'div',
        },
        infoBlock: {
          class: 'km-info-block',
          tag: 'div',
        },
        infoIntro: {
          class: 'kib-intro',
          tag: 'p',
        },
        infoList: {
          class: 'kib-list',
          tag: 'ul',
        },
        infoListItem: {
          class: 'kibl-item',
          tag: 'li',
        },
        infoListItemLine: {
          class: 'kibli-line-',
          tag: 'span',
        },
        choices: {
          class: 'km-filter-choices',
          tag: 'div',
        },
        select: {
          class: 'km-filter',
          counterPrefix: 'km-filter-',
        },
      },
      filters: [],
    };
    this.items = [];
    this.selects = [];
    this.props = this.mergeDeep(this.defaults, this.userProps);
    this.filteredCards = [];
    this.filterBarCount = 0;
    this.filterBar = null;
    this.callback = callback || null;
    this.init();
  }

  init() {
    const self = this;
    const props = this.props;

    // Populate filters from HTML
    if (props.config.fetchFilterFromHTML !== null) {
      self.fetchFilterFromHTML();
    }

    // Count existings filter bars
    if (props.config.countFilterBars) {
      self.filterBarCount = document.getElementsByClassName(
        props.html.filterBar.class
      ).length;
    }

    // Fetch initial items
    const items = Array.prototype.slice.call(
      document.querySelectorAll(props.config.items)
    );
    items.forEach((item) => self.items.push(item));

    // Populate filterBar
    self.filterBar = self.buildFilterBar();
    self.filterBar.appendChild(self.buildChoices());

    // create clear all button
    if (props.config.clearFiltersfunct) self.createClearAll();

    // Sort the list
    this.sort();

    // attach event listeners
    this.selects.forEach(function (select) {
      select.addEventListener('change', self.sort.bind(self), false);
    });
  }

  createClearAll() {
    const self = this;
    const props = self.props.config.clearFilters;
    const btn = document.createElement(props.tag);
    btn.className = props.classes;
    const content = document.createTextNode(props.text);
    btn.appendChild(content);

    btn.addEventListener('click', self.reset.bind(this), false);

    if (props.appendTo === null) {
      self.filterBar.appendChild(btn);
    } else {
      const parent = document.querySelector(props.appendTo);
      if (parent) parent.appendChild(btn);
    }
    return self;
  }

  reset() {
    const self = this;
    const selects = self.selects;
    selects.forEach(function (select, index) {
      let target = self.props.filters[index];
      select.selectedIndex = target.initial;
      self.sort();
    });
  }

  fetchFilterFromHTML() {
    const self = this;
    const source = document.querySelector(
      self.props.config.fetchFilterFromHTML
    );
    if (!source) return;

    const filters = Array.prototype.slice.call(source.children);

    filters.forEach(function (dataSource) {
      const catTitle = dataSource.getElementsByTagName('p')[0];
      const initialIndex = dataSource.getElementsByTagName('span')[0];
      const list = dataSource.getElementsByTagName('ul')[0];
      const options = Array.prototype.slice.call(
        list.getElementsByTagName('li')
      );

      const filter = {
        category: catTitle ? catTitle.textContent : 'Title',
        initial: initialIndex ? parseInt(initialIndex.textContent) : 0,
        options: options.map(function (opt) {
          return opt.textContent;
        }),
      };

      self.props.filters.push(filter);
    });

    return self;
  }

  populateSelectedValues() {
    const result = [];
    this.selects.forEach(function (select) {
      let selectData = {
        category: select.dataset.cat,
        value: select.options[select.selectedIndex].value,
      };
      result.push(selectData);
    });
    return result;
  }

  sort() {
    const self = this;
    const conditions = self.populateSelectedValues();
    const items = self.items.slice(); // make new copy
    const delimiter = self.props.config.itemDelimiter;
    const indexesToRemove = [];

    items.forEach(function (item) {
      let index = items.indexOf(item);

      conditions.forEach(function (con) {
        let cat = con.category.toLowerCase();
        let value = con.value.toLowerCase();
        if (value === self.props.config.valueForAllItems) return;

        if (!(cat in item.dataset)) {
          if (!(indexesToRemove.indexOf(index) > -1))
            indexesToRemove.push(index);
        } else {
          let valTrimmed = item.dataset[cat]
            .split(delimiter)
            .map(function (item) {
              return item.trim().toLowerCase();
            });
          if (
            valTrimmed.indexOf(value) === -1 &&
            !(indexesToRemove.indexOf(index) > -1)
          )
            indexesToRemove.push(index);
        }
      });
    });

    const result = items.filter(function (item, index) {
      return !(indexesToRemove.indexOf(index) > -1);
    });

    self.filteredCards = result;
    self.switchCards();
    if (self.props.config.populateInfoBar) self.populateInfoBar();
    if (self.callback) self.servePayload();
  }

  populateInfoBar() {
    const self = this;
    const props = self.props;
    const htmlConfig = props.html;
    const data = self.populateSelectedValues();

    // Remove old infoBar if it exists;
    if (self.filterBar.contains(self.infoBlock))
      self.filterBar.removeChild(self.infoBlock);

    const infoBlock = document.createElement(htmlConfig.infoBlock.tag);
    infoBlock.className = htmlConfig.infoBlock.class;

    if (props.config.infoIntro !== null) {
      const infoIntro = document.createElement(htmlConfig.infoIntro.tag);
      infoIntro.className = htmlConfig.infoIntro.class;
      const infoContent = document.createTextNode(props.config.infoIntro);
      infoIntro.appendChild(infoContent);
      infoBlock.appendChild(infoIntro);
    }

    const infoList = document.createElement(htmlConfig.infoList.tag);
    infoList.className = htmlConfig.infoList.class;

    data.forEach(function (object) {
      let item = document.createElement(htmlConfig.infoListItem.tag);
      item.className = htmlConfig.infoListItem.class;

      for (const property in object) {
        let string = object[property];
        let line = document.createElement(htmlConfig.infoListItemLine.tag);
        line.className = htmlConfig.infoListItemLine.class + property;
        let content = document.createTextNode(
          string.charAt(0).toUpperCase() + string.slice(1)
        );
        line.appendChild(content);
        item.appendChild(line);
      }

      infoList.appendChild(item);
    });

    infoBlock.appendChild(infoList);
    self.infoBlock = infoBlock;
    self.filterBar.appendChild(infoBlock);
  }

  switchCards() {
    const self = this;
    const props = this.props;
    const activeClass = props.config.activeClass;
    const resultClass = props.config.noResultsClass;
    const parent = document.querySelector(props.config.section);

    self.items.forEach(function (item) {
      item.classList.remove(activeClass);
    });

    self.filteredCards.forEach(function (item) {
      item.classList.add(activeClass);
    });

    if (self.filteredCards.length === 0 && props.config.resultClass !== null) {
      if (parent) parent.classList.add(resultClass);
    } else {
      if (parent) parent.classList.remove(resultClass);
    }
  }

  buildChoices() {
    const self = this;
    const props = self.props;
    const htmlConfig = props.html;
    const choices = document.createElement(htmlConfig.choices.tag);
    choices.className = htmlConfig.choices.class;

    this.props.filters.forEach(function (filter, index) {
      let select = document.createElement('select');
      select.className =
        htmlConfig.select.class +
        ' ' +
        htmlConfig.select.counterPrefix +
        (index + 1);
      select.dataset.cat = filter.category;
      let initialChoice = document.createElement('option');
      initialChoice.textContent = filter.category || 'Please select value';
      initialChoice.value = self.props.config.valueForAllItems;
      select.appendChild(initialChoice);
      self.selects.push(select);

      // Populate the rest of the children
      if ('options' in filter) {
        let options = filter.options;
        options.forEach(function (data) {
          let item = document.createElement('option');
          item.textContent = data;
          item.value = data;
          select.appendChild(item);
        });
      }

      // Initial active
      select.selectedIndex = filter.initial
        ? select.options[filter.initial]
          ? filter.initial
          : 0
        : 0;
      choices.appendChild(select);
    });
    return choices;
  }

  servePayload() {
    const self = this;
    const payload = {
      filterBar: self.filterBar,
      items: self.items,
      filteredCards: self.filteredCards,
      infoBlock: self.infoBlock,
    };
    self.callback(payload);
  }

  buildFilterBar() {
    const self = this;
    const props = this.props;
    const htmlConfig = props.html;
    const target = document.querySelector(props.config.section);
    const loc = props.config.location;
    const filterBar = document.createElement(htmlConfig.filterBar.tag);

    filterBar.className = htmlConfig.filterBar.class;

    if (loc === 'top') {
      target.insertBefore(filterBar, target.firstChild);
    } else {
      target.appendChild(filterBar);
    }

    if (props.config.countFilterBars) {
      filterBar.id =
        props.html.filterBar.class + '-' + (self.filterBarCount + 1);
    }

    return filterBar;
  }

  // Getter
  get getProps() {
    return this.props;
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
