function youtubeVideo(wrap = null, id = null, autoplay = true, config = {}, onReady = null) {
  const videoBlock = wrap.getElementsByClassName('km-yv-holder')[0];
  
  if(!wrap && isElement(wrap) || !id) return null;
  
  const playStateClass = 'km-yv-play-state';
  const pauseStateClass = 'km-yv-pause-state';
  const play = Array.from(wrap.querySelectorAll('.km-yv-play'));
  const pause = Array.from(wrap.querySelectorAll('.km-yv-pause'));
  
  const defaultPlayerConfig = {
    height: "100%",
    width: "100%",
    videoId: id, //'M7lc1UVf-VE',
    playerVars: {
      playlist: id, // key property for loop to work
      cc_load_policy: 0, // closed caption
      controls: 0,
      showinfo: 0,
      disablekb: 0, //disable keyboard
      iv_load_policy: 3, // annotations
      playsinline: 1, // play inline on iOS
      rel: 0, // related videos
      showinfo: 0, // title
      modestbranding: 1, // youtube logo
      autoplay: 1,
      loop: 1,
      fs: 0,
      suggestedQuality: "hd720",
      autohide: 0 // Hide video controls when playing
    },
    events: {
      onReady: onYoutubePlayerReady,
      onStateChange: onYoutubePlayerStateChange
    }
  };

  const playerConfig = mergeDeep(defaultPlayerConfig, config);

  window.YT.ready(function() {
     new YT.Player(videoBlock, playerConfig);
  })
  

  function onYoutubePlayerReady(event) {
    const player = event.target;

    player.mute();
    player.setPlaybackQuality("hd1080");
   
    if(!autoplay) {
      player.stopVideo();
      wrap.classList.add(pauseStateClass);
    } else {
       player.playVideo();
       wrap.classList.add(playStateClass);
    }

    // Play
    play.forEach(function(btn) {
       btn.addEventListener('click', function(){
         wrap.classList.remove(pauseStateClass);
         wrap.classList.add(playStateClass);
         player.playVideo();
       }, false);
    });

    // Pause
    pause.forEach(btn => {
       btn.addEventListener('click', () => {
         wrap.classList.remove(playStateClass);
         wrap.classList.add(pauseStateClass);
         player.pauseVideo();                                 
      }, false);
    });

    if(onReady) onReady(event);
    
  }

  function onYoutubePlayerStateChange(event) {
    var YTP = event.target;
    if (event.data === 1) {
      var remains = YTP.getDuration() - YTP.getCurrentTime();
      if (YTP.rewindTO) clearTimeout(YTP.rewindTO);
      YTP.rewindTO = setTimeout(function () {
        YTP.seekTo(0);
      }, (remains - 0.1) * 1000);
    }
  }

  function mergeDeep(...objects) {
    const isObject = (obj) => obj && typeof obj === "object";

    return objects.reduce((prev, obj) => {
      Object.keys(obj).forEach((key) => {
        const pVal = prev[key];
        const oVal = obj[key];

        if (Array.isArray(pVal) && Array.isArray(oVal)) {
          prev[key] = pVal.concat(...oVal);
        } else if (isObject(pVal) && isObject(oVal)) {
          prev[key] = mergeDeep(pVal, oVal);
        } else {
          prev[key] = oVal;
        }
      });

      return prev;
    }, {});
  }

 function isElement(element) {
    return element instanceof Element || element instanceof HTMLDocument;  
  }

}