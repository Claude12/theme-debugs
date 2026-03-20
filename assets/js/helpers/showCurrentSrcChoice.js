/**
 *  Displays current srcset used image if srcset is set.
 *  showCurrentSrcChoice();
 *  This function is fo dev purposes only and should not be included in final bundle.
 */
function showCurrentSrcChoice(){
  const images = Array.from(document.getElementsByTagName('img'))
  images.forEach(img => {
    if(img.hasAttribute('srcset')) {
      console.log(img.currentSrc);
    }
  });
}