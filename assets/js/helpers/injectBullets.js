// // Configure
// const config = {
//   bulletClass: 'km-custom-bullet', // class for bullet. all spans get abbreviation of this + type  // OPTIONAL
//   icon: 'bullet-icon', // optional. If present will add SVG ICON // OPTIONAL
//   numbers: true, // true - will add number, false - will not. default false // OPTIONAL
//   spacer: true, // populate empty span within bullet. useful if bullets is background image for example // OPTIONAL
//   parentClasses: ['km-bespoke-bullets'], // class names to add to parent ul // OPTIONAL
//   target: 'ul', // targeting all elements matching
//   exclude: ['excluded-one','excluded-two'] // target all from "target" as long as they do not contains any keys from 'exclude' // OPTIONAL
// }

// injectBullets(config);

function injectBullets(props) {
  const sel = props.target || 'ul';
  const elems = Array.prototype.slice.call(document.querySelectorAll(sel));
  if (elems.length === 0) return;

  const bulletClass = props.bulletClass || 'km-custom-bullet';
  const classAbbr =
    bulletClass
      .split('-')
      .map((key) => key[0])
      .join('') + '-';
  const icon = props.icon || null;
  const excludeClasses = props.exclude || [];
  const parentClasses = props.parentClasses || [];
  const numbers =
    typeof props.numbers === 'boolean' && props.numbers === true ? true : false;
  const spacer =
    typeof props.spacer === 'boolean' && props.numbers === true ? true : false;

  // Remove Those with exclude classes
  const items = elems.filter(function (element) {
    if (
      !excludeClasses.some((className) => element.classList.contains(className))
    )
      return element;
  });

  items.forEach((list) => {
    parentClasses.forEach((className) => list.classList.add(className));
    let listItems = Array.prototype.slice.call(list.children);
    listItems.forEach((item, index) => rebuild(item, index));
  });

  function rebuild(item, index) {
    const bullet = document.createElement('span');
    bullet.className = bulletClass;
    if (icon) bullet.appendChild(getIcon(icon));
    if (numbers) {
      const nr = document.createElement('span');
      nr.className = classAbbr + 'number';
      const nrContent = document.createTextNode(index + 1);
      nr.appendChild(nrContent);
      bullet.appendChild(nr);
    }
    if (spacer) {
      const spacerElem = document.createElement('span');
      spacerElem.classList.add(classAbbr + 'spacer');
      bullet.appendChild(spacerElem);
    }
    item.insertBefore(bullet, item.firstChild);
  }

  function getIcon(icon) {
    const svgElem = document.createElementNS(
      'http://www.w3.org/2000/svg',
      'svg'
    );
    svgElem.setAttribute('class', classAbbr + 'icon');
    const useElem = document.createElementNS(
      'http://www.w3.org/2000/svg',
      'use'
    );
    useElem.setAttributeNS(
      'http://www.w3.org/1999/xlink',
      'xlink:href',
      '#' + icon
    );
    svgElem.appendChild(useElem);
    return svgElem;
  }
}
