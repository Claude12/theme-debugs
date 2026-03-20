/**

 - Lazy load background images
 
**/
function lazyBgImage(item,observer) {
  const image = item.target;
  image.classList.remove("lazy-bg-image");
  observer.unobserve(image);
}


