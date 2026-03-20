window.addEventListener('DOMContentLoaded', function () {
  if (window.acf) {
    window.acf.addAction(
      'render_block_preview',
      post_type_set_1598005815494_init
    );
    window.acf.addAction('remount', post_type_set_1598005815494_init);
  } else {
    post_type_set_1598005815494_init();
  }

  function post_type_set_1598005815494_init() {
    // Load posts
    const postItemBlocks = Array.prototype.slice.call(
      document.querySelectorAll('.km-post-type-block')
    );

    postItemBlocks.forEach((pib, index) => {
      let cards = Array.prototype.slice.call(
        pib.querySelectorAll('.km-post-type-block .km-sortable-card')
      );
      let categories = Array.prototype.slice.call(
        pib.querySelectorAll('.km-ptc-cat')
      );
      //let allCatsBtn = pib.querySelector('.km-ptc-catd-all');

      let instance = new CategorySorter({
        categories,
        cards,
        initialCats: ['km-all-cats'], // ability to add categories ids that should be selected initially,
        //enableAllCatsInitially: true,
        allowMultipleCats: false, // if false - only one category is allowed at a time
        cardSelector: '.km-sortable-card', // If set. you can refersh sorting based on card selector. Useful with Ajax call where you can refresh sorting after fetching posts
        classes: {
          selectedCat: 'km-ptc-selected', // applied to category element (trigger)
          activeCard: 'km-ptc-active', // applied to active card ( if matches category)
        },
      });

      let loadMore = pib.querySelector('.km-items-load-more');
      new LazyPosts(
        {
          notifyElements: [pib],
          elems: {
            resultWrap: pib.querySelector('.km-post-type-block .km-ptf-wrap'),
            loadMore: loadMore,
          },
          attributes: {
            targetUrl: loadMore.dataset.targetUrl,
            totalPages: loadMore.dataset.totalPages,
            step: loadMore.dataset.step,
            postType: loadMore.dataset.postType,
            pageId: loadMore.dataset.pageId,
            orderBy: loadMore.dataset.orderBy,
            orderDirection: loadMore.dataset.orderDirection,
            singleMarkup: loadMore.dataset.singleMarkup,
            blockIndex: index + 1,
            currentpage: loadMore.dataset.current
          },
          config: {
            useStorage: loadMore.dataset.persistProgress === 'true',
          },
        },
        () => {
          instance.refresh;
        }
      );
    });
  }

});

