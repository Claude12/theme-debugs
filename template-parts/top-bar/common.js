window.addEventListener('DOMContentLoaded', (event) => {
  (function altTopBar() {
    const topBar = document.querySelector('.top-bar');
    const isAdmin = document
      .getElementsByTagName('body')[0]
      .classList.contains('wp-admin');

    if (!topBar || isAdmin) return;

    const altState = topBar.dataset.alt === 'false' ? null : topBar.dataset.alt;

    if (!altState) return;
    scrollClass(
      {
        element: topBar,
        class: 'top-bar-scroll',
        addAfter: altState,
      }
      //controlLogo // enable if you want animation on letter removal
    );
    scrollClass(
      {
        element: document.body,
        class: 'top-bar-alt',
        addAfter: altState,
      }
      //controlLogo // enable if you want animation on letter removal
    );
  }
  )();

  scrollClass(
  {
    element: document.body,
    class: 'top-bar-visible',
    addAfter: 200,
  });

  // Show top bar on pages that cannot scroll
  if(document.body.scrollHeight + 200 < document.documentElement.clientHeight) {
    document.body.classList.add('top-bar-visible');
  }
});
