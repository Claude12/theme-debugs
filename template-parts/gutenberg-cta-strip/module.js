

function cta_strip_init() {
  const sections = Array.prototype.slice.call(
    document.getElementsByClassName('kcs-has-separator')
  );

  sections.forEach((section) => {
    let btns = Array.prototype.slice.call(
      section.getElementsByClassName('kcs-cta')
    );

    let ele = document.createElement('span');
    ele.classList.add('kcs-separator');
    let string = document.createTextNode('or');
    ele.appendChild(string);

    btns.forEach((btn, index) => {
      if (btns.length - 1 !== index) {
        btn.parentNode.insertBefore(ele, btn.nextSibling);
      }
    });
  });
}
cta_strip_init();
