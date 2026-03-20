/*
 * Function adds/removes click event on certain media
 * props :
 * {
 *  selector: '.trigger-selector', // Function will return if no elements found
 *  media: 'media condition'// DEFAULT: (max-width:980px)
 *  onClick: function to be executed on click
 *  onMediaChange: function to be executed on media condition change. Matches or not. Function if set will receive two arguments. 1 arg - *  array set of matching trigger selectors, 2. arg - true/false ( media matches or not )
 * }
 */
function conditionalClick(props) {
  const mediaValue = props.media || '(max-width: 980px)';
  const mediaCheck = window.matchMedia(mediaValue);
  const elems = Array.prototype.slice.call(document.querySelectorAll(props.selector));

  if (elems.length === 0) return;

  function controlEvents(mediaCheck) {
    elems.forEach(function(elem) {
      mediaCheck.matches ? elem.addEventListener('click', exec, false) : elem.removeEventListener('click', exec, false);
    });

    if (props.onMediaChange) props.onMediaChange(elems, mediaCheck.matches);
  }

  function exec(e) {
    if (props.onClick) props.onClick(e);
    else console.error('Callback function is not set');
  }

  mediaCheck.addListener(controlEvents);
  controlEvents(mediaCheck);
}
