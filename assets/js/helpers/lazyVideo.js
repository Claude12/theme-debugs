/**
 *  Plays and pauses lazy video
 *  example
 * 
 * window.addEventListener('DOMContentLoaded', () => {
 *   kmIntersect(Array.from(document.querySelectorAll(".lazy-bg-video")), {
 *     observerProps: {
 *       threshold: [0.9] // 90% of element is visible
 *     },
 *     on: (item,observer) => loadLazyVideo(item,observer),
 *     off: (item,observer) => pauseLazyVideo(item,observer)
 *   });
 * });
 * add "fit-to-parent" to video tag if you want video to adjust its dimensions automatically.
 * add "stop-autoplay" to videa as attribute to lazy load video but do not autoplay
 * add "is-swapped-video" to swap video
*/

function loadLazyVideo(item, observer) {
  const lazyClass = 'lazy-bg-video';
  const video = item.target;
  const fitParent = video.hasAttribute('fitparent'); // make video centered and cover desired area using JS
  const autoPlays = video.hasAttribute('autoplays'); 

  if(video.classList.contains(lazyClass)) {
     for (var source in video.children) {
      var videoSource = video.children[source];
      if ( typeof videoSource.tagName === "string" && videoSource.tagName === "SOURCE") {
        videoSource.src = videoSource.dataset.src;
      }
    }
    
    video.load();
    video.classList.remove(lazyClass);

    if(fitParent){
      video.parentElement.classList.add('js-controlled-video');
      fitVideo(video.parentElement, video); 
    }

    if(autoPlays){
      video.play();
    } 
  } else {
    if(autoPlays) video.play();
  }

  video.classList.remove('km-video-paused');
}

function pauseLazyVideo(item, observer) {
  const video = item.target;
  const autoPlays = video.hasAttribute('autoplays'); 
  if (video.tagName !== "VIDEO") return;

  if(autoPlays) {
    video.pause();
    video.classList.add('km-video-paused');
  }
}
