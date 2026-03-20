/*
 *
 * id = id of icon symbol
 * className = extra class name to be added to the icon;
 * tag = tag name for the icon
 *
 */
function createIcon(id, className, tag) {
  const wrap = document.createElement(tag);
  wrap.className = className;
  const svgElem = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  const useElem = document.createElementNS('http://www.w3.org/2000/svg', 'use');
  useElem.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', id);
  svgElem.appendChild(useElem);
  wrap.appendChild(svgElem);
  return wrap;
}
