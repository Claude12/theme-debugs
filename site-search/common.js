/**
 * Site Search
 * Functionality:
 * Opens on click
 * Closes on off click or ESC
 * Submits on return
 **/

function siteSearch() {
  const items = Array.from(document.getElementsByClassName("km-search-form"));
  const active = 'search-active';

  items.forEach((item) => {
    let input = item.getElementsByClassName("km-search-input")[0];
    let icon = item.getElementsByClassName("km-search-icon")[0];
    icon.addEventListener("click", controlSearch, false);

    function controlSearch(e) {
      item.classList.toggle(active);
      setOffClick(item, e);
    }

    function setOffClick(item, e) {
      if (item.classList.contains(active)) {
        document.addEventListener("click", offClick, false);
        document.addEventListener("keydown", offKey, false);
      }
    }

    function offClick(e) {
      if (!item.contains(e.target)) {
        resetState();
        document.removeEventListener("click", offClick, false);
      }
    }

    function offKey(e) {
      if (e.code === "Escape") {
        resetState();
        document.removeEventListener("keydown", offKey, false);
      }
    }

    function resetState() {
      if (document.activeElement != document.body) document.activeElement.blur();
      input.value = "";
      item.classList.remove(active);
    }
  });
}

window.addEventListener("DOMContentLoaded", () => {
  siteSearch();
});
