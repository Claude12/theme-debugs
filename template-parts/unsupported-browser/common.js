
//unsupportedBrowser(false); // pass true and you will see information about browser


/**
 *
 * @param {Boolean} isDev
 * Triggers browser check
 */
function unsupportedBrowser(isDev) {
  const unsupportedBrowsers = {
    Chrome: 80, // version 80 and below is not supported
    Firefox: 60,
    IE: 11,
    Edge: 15, // not chromium
    Opera: 65,
    Safari: 11,
  };

  const ub = document.getElementById('unsupported-browser');
  if (!ub) return null;

  hookDissmissButtons(ub);
  initBrowserSupport(ub, unsupportedBrowsers, isDev);
}

/**
 *
 * @param {DOM element} parent
 * Attaches click event to dismiss notification and proceed to the site
 */
function hookDissmissButtons(parent) {
  const dismissBtns = Array.prototype.slice.call(
    parent.querySelectorAll('.ubb-dismiss-btn')
  );

  dismissBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      parent.classList.add('ubb-dismissed');
    });
  });
}

/**
 *
 * @param {DOM element} parent
 * @param {Object} browserList // object of browsers you do not support
 * @param {Boolean} isDev
 */
function initBrowserSupport(parent, browserList, isDev) {
  const currentBrowser = new BrowserDetector(browserList);
  const noSupportClass = 'ubb-not-supported';

  if (!currentBrowser.isSupported()) {
    parent.classList.add(noSupportClass);
  } else {
    parent.classList.remove(noSupportClass);
  }

  if (isDev) {
    const ugly = currentBrowser.showInfo;
    const obj = JSON.parse(ugly);
    const pretty = JSON.stringify(obj, undefined, 4);
    console.log(pretty);
    // Enable below to test results on mobile devices    
    // const box = document.createElement('textarea');
    // box.value = pretty;
    // box.style.width = '100vw';
    // box.style.height = '100vh';
    // document.querySelector('.main-content-wrap').appendChild(box);
  }

}