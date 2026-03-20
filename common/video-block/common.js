function initVideos() {
  const videoBlocks = Array.prototype.slice.call(
    document.querySelectorAll('.km-video-block')
  );

  videoBlocks.forEach((inst) => {
    let muteToggle = inst.getElementsByClassName('km-video-mute')[0];

    let mute = inst.dataset.mute;
    let autop = inst.dataset.autoplay;
    let enableMuteToggle = mute ? (mute === '1' ? true : false) : false;
    let autoplay = autop ? (autop === '1' ? true : false) : false;

    let props = {
      customControls: '(min-width:100px)', // match = custom controls
      context: {
        videoContainer: inst,
        video: inst.getElementsByTagName('video')[0],
        playBtn: inst.getElementsByClassName('km-video-play')[0],
        pauseBtn: inst.getElementsByClassName('km-video-pause')[0],
      },
      config: {
        initControls: true,
        autoplay: false,
      },
    };

    if (enableMuteToggle) props.context.muteToggle = muteToggle;
    if (autoplay) props.config.autoplay = autop;

    new KetchupVideoBg(props);
  });
}

initVideos();