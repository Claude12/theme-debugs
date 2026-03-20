class LazyPosts {
  constructor(userProps = {}, cb = null) {
    this.defaults = {
      elems: {
        resultWrap: null,
        loadMore: null,
      },
      attributes: {
        targetUrl: null,
        totalPages: 1,
        step: 1,
        postType: 'post',
        pageId: null,
        blockIndex: null,
        orderDirection: 'DESC',
        singleMarkup: null, // file where single item gets passed in for HTML
        orderBy: 'date',
      },
      config: {
        ajaxAction: 'km_load_more_posts',
        useStorage: true, // save data in local storage?
        dataKey: 'km_post_type_pages',
      },
      strings: {
        idle: 'Load More',
        loading: 'Loading...',
        end: 'No more items to show',
      },
      notifyElements: [], // List of DOM element that will get class name during different stages of block
      classes: {
        idle: 'km-items-idle',
        loading: 'km-items-loading',
        end: 'km-items-end',
      },
    };
    this.cb = cb;
    this.props = this.mergeDeep(this.defaults, userProps);
    this.loadPosts = this.loadPosts.bind(this);
    this.removeSavedData = this.removeSavedData.bind(this);
    this.page = 1;
    this.initialLoad = true;

    if (!this.props.attributes.targetUrl)
      throw new Error('URL for admin-ajax is not set.');

    if (this.props.config.useStorage) {
      this.savePage();
      this.fetchCurrentPage();
    } else {
      this.removeSavedData();
    }

    this.init();
    this.loadPosts();
  }

  isELe(element) {
    return element instanceof Element || element instanceof HTMLDocument;
  }

  init() {
    const props = this.props.elems;
    const pageProps = this.props.attributes;
    const btn = this.props.elems.loadMore;

    if (!this.isELe(props.resultWrap) || !this.isELe(props.loadMore))
      throw new Error('"resultWrap" and "loadMore" should be DOM elements');

    if (pageProps.totalPages === this.page || parseInt(pageProps.step) === -1) {
      this.notifyElements('end');
    } else {
      btn.disabled = false;
      props.loadMore.addEventListener('click', this.loadPosts, false);
    }
  }

  fetchCurrentPage() {
    const key = this.props.config.dataKey;
    const src = localStorage.getItem(key);
    const currentData = src ? JSON.parse(src) : [];

    let currentPage = this.page;

    currentData.forEach((item) => {
      if (this.recordMaches(item)) currentPage = item.page;
    });

    this.page = currentPage;
  }

  removeSavedData() {
    const key = this.props.config.dataKey;
    const src = localStorage.getItem(key);
    const currentData = src ? JSON.parse(src) : [];
    const newData = currentData.filter((set) => !this.recordMaches(set));

    if (newData.length > 0) {
      localStorage.setItem(key, JSON.stringify(newData));
    } else {
      localStorage.removeItem(key);
    }
  }

  savePage() {
    const key = this.props.config.dataKey;
    const targetSlug = this.props.attributes.postType;
    const pageId = this.props.attributes.pageId;
    const blockIndex = this.props.attributes.blockIndex;
    const src = localStorage.getItem(key);
    const currentData = src ? JSON.parse(src) : [];

    if (!currentData.some((set) => this.recordMaches(set))) {
      currentData.push({
        pageId: pageId,
        slug: targetSlug,
        page: this.page,
        blockIndex: blockIndex,
      });
    }

    localStorage.setItem(key, JSON.stringify(currentData));
  }

  updatePage() {
    const key = this.props.config.dataKey;
    const src = JSON.parse(localStorage.getItem(key));
    const newData = src.map((item) => {
      if (this.recordMaches(item)) {
        item.page = this.page;
      }
      return item;
    });

    localStorage.setItem(key, JSON.stringify(newData));
  }

  recordMaches(record) {
    const targetSlug = this.props.attributes.postType;
    const pageId = this.props.attributes.pageId;
    const blockIndex = this.props.attributes.blockIndex;

    return (
      record.slug === targetSlug &&
      record.pageId === pageId &&
      record.blockIndex === blockIndex
    );
  }

  loadPosts() {
    const totalPages = parseInt(this.props.attributes.totalPages);
    const postType = this.props.attributes.postType;
    const singleMarkup = this.props.attributes.singleMarkup;
    const step = this.props.attributes.step;
    const orderBy = this.props.attributes.orderBy;
    const orderDirection = this.props.attributes.orderDirection;
    const ajaxUrl = this.props.attributes.targetUrl;

    if (!this.initialLoad) this.page++;

    const props = {
      action: this.props.config.ajaxAction,
      page: this.page,
      post_type: postType,
      step: step,
      order_by: orderBy,
      order_direction: orderDirection,
      single_markup: singleMarkup,
    };

    this.notifyElements('loading');

    axios
      .post(ajaxUrl, null, {
        params: props,
      })
      .then((response) => {
        this.updateResult(response.data);
        this.notifyElements('idle');

        if (this.props.config.useStorage) this.updatePage();

        //console.log('RESULT', response.data);

        // Run funstion on end if one is provided.
        if (this.cb) {
          this.cb({
            instance: this,
            data: response.data,
          });
        }

        if (totalPages === this.page) {
          this.notifyElements('end');
          return;
        }

        this.initialLoad = false;
      })
      .catch((error) => {
        console.error(error);
      });
  }

  notifyElements(state) {
    const elements = this.props.notifyElements;
    const loadMore = this.props.elems.loadMore;
    elements.push(loadMore);
    this.updateButtonContent(state, loadMore);

    elements.forEach((element) => {
      if (!this.isELe(element))
        throw new Error('"' + element + '"' + ' should be a DOM element');
      this.controlState(state, element);
    });
  }

  updateButtonContent(state, btn) {
    const strings = this.props.strings;
    switch (state) {
      case 'idle':
      default:
        btn.disabled = false;
        btn.innerText = strings.idle;
        break;
      case 'loading':
        btn.disabled = true;
        btn.innerText = strings.loading;
        break;
      case 'end':
        btn.disabled = true;
        btn.innerText = strings.end;
    }
  }

  controlState(state, element) {
    const classes = this.props.classes;
    switch (state) {
      case 'idle':
      default:
        this.swapClassState(classes.idle, element);
        break;
      case 'loading':
        this.swapClassState(classes.loading, element);
        break;
      case 'end':
        this.swapClassState(classes.end, element);
    }
  }

  swapClassState(newClass, element) {
    const classes = this.props.classes;
    element.classList.remove(classes.idle, classes.loading, classes.end);
    element.classList.add(newClass);
  }

  updateResult(data) {
    const props = this.props.elems;
    const resultWrap = props.resultWrap;
    while (resultWrap.firstChild) resultWrap.removeChild(resultWrap.firstChild);
    resultWrap.insertAdjacentHTML('beforeend', data);
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
