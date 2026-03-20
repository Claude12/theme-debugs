// IIFE for Cookie notification
(function() {
  const cookieKey = 'KMCookiesAccepted';
  const barActive = 'kmc-active';
  const cookieAcceptBtn = '#kmc-cookies-accept';
  const cookiesAccepted = localStorage.getItem(cookieKey);
  const cookieBar = document.querySelector('.km-cookie-notification');

  if (!cookieBar) return;

  if (cookiesAccepted === null) showNotification();

  function showNotification() {
    const btn = document.querySelector(cookieAcceptBtn);
    cookieBar.classList.add(barActive);
    btn.addEventListener('click', acceptCookie, false);
  }

  function acceptCookie() {
    localStorage.setItem(cookieKey, 'true');
    cookieBar.classList.remove(barActive);
  }
})();

