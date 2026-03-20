function googleDirections(){
  const elems = Array.from(document.getElementsByClassName('km-google-directions'));

  elems.forEach(instance => instance.addEventListener("submit", openDirections, false));

  function openDirections(e){
    e.preventDefault();
    let form = e.currentTarget;
    let input = form.elements['gd-param'];
    let value = encodeURIComponent(input.value);
    let dest = form.elements['km-gd-btn'].dataset.dest;
    if(value !== '') {
      window.open(dest + value);
      intput.value = '';
    }
  }
}

googleDirections();


