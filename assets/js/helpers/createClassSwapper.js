/**
 * 
 * @param {Object} props Object for ClassSwapper initiation
 * 
 *  
 * createClassSwapper({
    media: '(min-width:320px)', // optional
    element : document.querySelector('.menu'),
    match: barMenu.menu_bg.colour.slug,
    noMatch : pushMenu.menu_bg.colour.slug,
    property : 'background-color',
    extraMatch: ['optional-bar'], // optional
    extraNoMatch: ['optional-push'], // optional
  })
 */

function createClassSwapper(props) {
  let match = [];
  let noMatch = [];

  if (props.match) {
    match = [`has-${props.match}-${props.property}`];
  }

  if (props.noMatch) {
    noMatch = [`has-${props.noMatch}-${props.property}`];
  }

  if (props.extraMatch) match = match.concat(props.extraMatch);
  if (props.extraNoMatch) noMatch = noMatch.concat(props.extraNoMatch);

  const swapperProps = {
    match: match,
    noMatch: noMatch,
  };

  if (props.element) swapperProps.element = props.element;
  if (props.elements) swapperProps.elements = props.elements;

  if (props.media) swapperProps.media = props.media;
  new ClassSwapper(swapperProps);
}
