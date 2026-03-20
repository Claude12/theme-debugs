/**
 *  Fits video within its container.
 *  @param {DOM Object} video wrapper
 *  @param {DOM Object} video tag
 */

function fitVideo(videoContainer,videoElement) {
  function updateVideo() {
    var container_width = videoContainer.offsetWidth;
    var container_height = videoContainer.offsetHeight;
    videoElement.style.height = 'auto';
    videoElement.style.width = container_width + 'px';
    if (videoElement.offsetHeight < container_height) {
      videoElement.style.height = container_height + 'px';
      videoElement.style.width = 'auto';
    }
    videoElement.style.top = ((videoElement.offsetHeight - container_height) / 2) * -1 + 'px';
    videoElement.style.left = ((videoElement.offsetWidth - container_width) / 2) * -1 + 'px';
  }

  window.addEventListener('load', updateVideo);
  window.addEventListener('resize', updateVideo);
};