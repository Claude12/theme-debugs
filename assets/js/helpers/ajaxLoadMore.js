// window.addEventListener('DOMContentLoaded', function() {
//   const config = {
//     ajaxEndpoint: window.location.origin + '/wp-admin/admin-ajax.php', // AJAX URL
//     btn: '.load-more:not(.disabled)', // Button selector
//     btnDisabledClass: 'disabled', // same as in btn :not for example,
//     appendTo: '.posts-list', // apend posts to
//     storageKeyName: 'km-pg', // name of key stored in storage
//     loader: '.posts-loader', // loader selector - optional. if not present - class name will not be added.
//     loaderActive: 'active' // active class name for loader,
//     devHost : 'www.ketchupdevelopment.co.uk', // optional
//     devSub: 'TreasureTransport' // sub that appears on dev - for example www.ketchupdevelopment.co.uk/TreasureTransport would mean 'TreasureTransport'/ Leave blank if unknown whilst developing
//   };

//   loadMorePosts(config);
// });

function loadMorePosts(config, cb) {
  const props = config || {};
  const btnSel = props.btn || '.load-more:not(.disabled)';
  const targetSel = props.appendTo || '.posts-list';
  const loaderSel = props.loader || '.posts-loader';
  const totalAttr = props.totalAttr || 'total';
  const isDev =
    window.location.host === props.devHost ||
    window.location.host === 'www.ketchupdevelopment.co.uk';
  const sub = isDev ? '/' + props.devSub + '/' || '/' : '/';
  const ajaxUrl =
    props.ajaxEndpoint ||
    window.location.origin + sub + 'wp-admin/admin-ajax.php';
  const btn = document.querySelector(btnSel);
  const target = document.querySelector(targetSel);
  const sessionKey = props.sessionKey || 'km-pg';
  const loaderActiveClass = props.loaderActive || 'active';
  const disabledBtnClass = props.btnDisabledClass || 'disabled';

  if (!btn || !target) return;
  const maxPages = parseInt(btn.dataset[totalAttr]);
  const savedPage = sessionStorage.getItem(sessionKey);
  const loader = document.querySelector(loaderSel);

  if (!maxPages)
    throw new Error(targetSel + ' is missing data-' + totalAttr + 'attribute ');

  if (savedPage) fetchPosts(true);
  if (!savedPage) sessionStorage.setItem(sessionKey, 1);

  btn.addEventListener(
    'click',
    function (e) {
      e.preventDefault();
      fetchPosts(false);
    },
    false
  );

  function fetchPosts(initialFetch) {
    let page = parseInt(sessionStorage.getItem(sessionKey));
    let params = new URLSearchParams();
    params.append('action', 'load_more');
    let step = page;
    // params.set('page', params);
    params.set('page', step);

    //console.log(params.toString(), initialFetch);
    if (loader) loader.classList.add(loaderActiveClass);
    btn.classList.add(disabledBtnClass);

    axios.post(ajaxUrl, params).then(function (res) {
      if (maxPages - 1 === step) btn.parentNode.removeChild(btn);

      if (!initialFetch) {
        step++;
        sessionStorage.setItem(sessionKey, step);
      }

      if (loader) loader.classList.remove(loaderActiveClass);
      btn.classList.remove(disabledBtnClass);

      target.insertAdjacentHTML('beforeend', res.data);
      if (cb) cb();
    });
  }

  if (maxPages === 1) {
    if (btn) btn.parentNode.removeChild(btn);
  }

  if (savedPage && savedPage > 1) {
    while (target.firstChild) {
      target.removeChild(target.firstChild);
    }

    for (let i = 1; i <= savedPage; i++) {
      let params = new URLSearchParams();
      params.append('action', 'load_more');
      params.set('page', i - 1);

      if (loader) loader.classList.add(loaderActiveClass);
      btn.classList.add(disabledBtnClass);
      axios.post(ajaxUrl, params).then(function (res) {
        target.insertAdjacentHTML('beforeend', res.data);
        if (maxPages === i) {
          btn.parentNode.removeChild(btn);
          btn.classList.remove(disabledBtnClass);
        }
        if (loader) loader.classList.remove(loaderActiveClass);
      });
    }
  }
}
