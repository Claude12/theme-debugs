 /**
 * 
 * @param {Array} videos Array of video DOM elements
 * videoControls(Array.from(document.getElementsByClassName('km-mb-video')));
 */

  function videoControls(videos = []){
  
    videos.forEach(video => {
      let parent = video.parentElement;
      let audioBtn = parent.getElementsByClassName('km-mb-audio-control')[0];
      let stateControlBtn = parent.getElementsByClassName('km-mb-state-control')[0];
      let fitParent = video.hasAttribute('fitparent');

        // Control Audio
        if(audioBtn) {
          let audioTarget = audioBtn.getElementsByClassName('km-icon')[0] || audioBtn;
          audioBtn.addEventListener('click', function() {

          audioTarget.classList.remove('km-icon-sound-on','km-icon-sound-off');
          if (video.muted === true) {
            video.muted = false;
            audioTarget.classList.add('km-icon-sound-on');
          }
          else if (video.muted === false) {
            video.muted = true;
            audioTarget.classList.add('km-icon-sound-off');
          }
          });
        }

        // Control play state
        if(stateControlBtn) {
          let stateTarget = stateControlBtn.getElementsByClassName('km-icon')[0] || stateControlBtn;
          stateTarget.addEventListener('click', function() {

          // Video might have black borders if not played straight away. function below recalculates area
          if(fitParent) fitVideo(video.parentElement, video, true); 

          stateTarget.classList.remove('km-icon-play','km-icon-pause');
          if (video.paused) {
            video.play();
            stateTarget.classList.add('km-icon-pause');
          }
          else{
            video.pause();
            stateTarget.classList.add('km-icon-play');
          }
          });
        }
    });
  }