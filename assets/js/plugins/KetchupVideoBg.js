const KetchupVideoBg = function (opts, callback) {
  if (!(this instanceof KetchupVideoBg))
    return new KetchupVideoBg(opts, callback);

  const defaults = {
    customControls: '(min-width:981px)', // match = custom controls
    context: {
      videoContainer: null,
      video: null,
      playBtn: null,
      pauseBtn: null,
      muteToggle: null,
    },
    classes: {
      activeBtn: 'video-btn-active', // assigned to active button
      customControlsClass: 'km-video-custom-controls', // added to container if media matches
      muteActive: 'km-video-mutted',
    },
    config: {
      initControls: true, // initiate video control buttons?
      autoplay: true, // if false - video vill not autoplay.
    },
  };

  const userData = opts || {};
  this.props = this.mergeObj(true, defaults, userData);
  this.callback = callback || null;
  this.mediaCheck = window.matchMedia(this.props.customControls);
  this.mediaMatches = false;
  this.videoContainer = this.props.context.videoContainer;
  this.videoElement = this.props.context.video;
  this.playBtn = this.props.context.playBtn;
  this.pauseBtn = this.props.context.pauseBtn;

  if (!this.videoContainer || !this.videoElement) return null;
  this.init();
  return this;
};

KetchupVideoBg.prototype.init = function () {
  const self = this;
  const muteClass = this.props.classes.muteActive;
  const muteToggle = this.props.context.muteToggle;

  // Listen to state changes
  this.mediaCheck.addListener(self.detectView.bind(self));
  this.detectView.call(self, self.mediaCheck);

  // Initiate Controls
  if (
    this.props.config.initControls === true &&
    this.playBtn &&
    this.pauseBtn
  ) {
    self.controlBtnClasses();

    this.pauseBtn.addEventListener(
      'click',
      function () {
        self.videoElement.pause();
        self.controlBtnClasses();
      },
      false
    );

    this.playBtn.addEventListener(
      'click',
      function () {
        self.videoElement.play();
        self.controlBtnClasses();
      },
      false
    );
  }

  // Mute toggle
  if (this.props.context.muteToggle) {
    this.props.context.muteToggle.addEventListener(
      'click',
      function () {
        self.videoElement.muted = !self.videoElement.muted;
        if (muteClass && muteToggle) muteToggle.classList.toggle(muteClass);
      },
      false
    );
  }

  if (muteClass && muteToggle && self.videoElement.muted)
    muteToggle.classList.add(muteClass);
  return this;
};

KetchupVideoBg.prototype.controlBtnClasses = function () {
  const videoElement = this.videoElement;
  const activeClass = this.props.classes.activeBtn;

  if (videoElement.paused === true) {
    this.playBtn.classList.add(activeClass);
    this.pauseBtn.classList.remove(activeClass);
  } else {
    this.pauseBtn.classList.add(activeClass);
    this.playBtn.classList.remove(activeClass);
  }
};

KetchupVideoBg.prototype.responsiveAction = function () {
  const videoContainer = this.videoContainer;
  const videoElement = this.videoElement;
  const containerClass = this.props.classes.customControlsClass;

  //console.log(videoElement);
  // Edge Fix where it always autoplays video
  if (videoElement && videoElement.paused === false) {
    videoElement.pause();
  }
  if (containerClass) videoContainer.classList.remove(containerClass);
  videoElement.setAttribute('controls', 'controls');
  videoElement.removeAttribute('autoplay');

  videoElement.removeAttribute('style');
  videoElement.controlsList = 'nodownload';
  videoElement.load();
  videoElement.preload = 'auto';
};

KetchupVideoBg.prototype.desktopAction = function () {
  const videoContainer = this.videoContainer;
  const videoElement = this.videoElement;
  const containerClass = this.props.classes.customControlsClass;

  // Below now achieved via CSS
  // function updateVideo() {
  //   var container_width = videoContainer.offsetWidth;
  //   var container_height = videoContainer.offsetHeight;
  //   videoElement.style.height = 'auto';
  //   videoElement.style.width = container_width + 'px';
  //   if (videoElement.offsetHeight < container_height) {
  //     videoElement.style.height = container_height + 'px';
  //     videoElement.style.width = 'auto';
  //   }
  //   videoElement.style.top =
  //     ((videoElement.offsetHeight - container_height) / 2) * -1 + 'px';
  //   videoElement.style.left =
  //     ((videoElement.offsetWidth - container_width) / 2) * -1 + 'px';
  // }

  // window.addEventListener('load', updateVideo);
  // window.addEventListener('resize', updateVideo);
  videoElement.removeAttribute('controls');
  if (containerClass) videoContainer.classList.add(containerClass);

  if (this.props.config.autoplay) {
    videoElement.autoplay = true;
    videoElement.play();
  }
};

KetchupVideoBg.prototype.detectView = function (query) {
  if (query.matches) {
    this.mediaMatches = true;
    this.desktopAction();
  } else {
    this.mediaMatches = false;
    this.responsiveAction();
  }

  if (
    this.props.config.initControls === true &&
    this.playBtn &&
    this.pauseBtn
  ) {
    this.controlBtnClasses();
  }
  return this;
};

KetchupVideoBg.prototype.mergeObj = function () {
  // Variables
  const self = this;
  var extended = {};
  var deep = false;
  var i = 0;
  var length = arguments.length;

  // Check if a deep merge
  if (Object.prototype.toString.call(arguments[0]) === '[object Boolean]') {
    deep = arguments[0];
    i++;
  }

  // Merge the object into the extended object
  var merge = function (obj) {
    for (var prop in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, prop)) {
        // If deep merge and property is an object, merge properties
        if (
          deep &&
          Object.prototype.toString.call(obj[prop]) === '[object Object]'
        ) {
          extended[prop] = self.mergeObj(true, extended[prop], obj[prop]);
        } else {
          extended[prop] = obj[prop];
        }
      }
    }
  };

  // Loop through each object and conduct a merge
  for (i = 0; i < length; i++) {
    var obj = arguments[i];
    merge(obj);
  }

  return extended;
};
