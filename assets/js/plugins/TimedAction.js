class TimedAction {
  constructor(userProps) {
    this.userProps = userProps || {};

    let defaults = {
      time: 10,
      sskey: 'timed-popout', // Session storage key
      interval: 1000,
      onEveryStep: 2, // used if on.every is set
      autoInit: true,
      destroyOnEnd: true, // destroy counter once it reached goal
      on: {
        end: null, // executed on time end (after this.props.time)
        increment: null, // executed on every interval
        every: null // integer. example - 3 . function executed every third count. if ten - 3,6,9
      }
    };

    this.boundUpdate = this.updateTime.bind(this);
    this.props = this.mergeDeep(defaults, this.userProps);
    this.clear = this.clearCount.bind(this);
    this.stop = this.stopCount.bind(this);
    this.destroy = this.destroyCounter.bind(this);
    this.start = this.init.bind(this);

    // If session is the same and popout is already complete
    const hasEnded = sessionStorage.getItem(this.props.sskey);

    if (hasEnded && parseInt(hasEnded) === this.props.time) return;

    if (this.props.autoInit && typeof this.props.autoInit === 'boolean') this.boundUpdate();
  }

  updateTime() {
    const sskey = this.props.sskey;
    const existingSskey = sessionStorage.getItem(sskey);
    const time = existingSskey ? existingSskey : 0;
    const nextInterval = parseInt(time) + 1;

    const payload = {
      current: this.props.time,
      next: nextInterval
    };

    // Run on every count
    if (this.props.on.increment) {
      payload.source = 'increment';
      this.props.on.increment(payload);
    }

    // run every function on every x count
    if (nextInterval % this.props.onEveryStep == 0) {
      if (this.props.on.every) {
        payload.source = 'every';
        this.props.on.every(payload);
      }
    }

    // Run at the end of count
    if (parseInt(time) >= parseInt(this.props.time)) {
      if (this.props.destroyOnEnd === true) this.destroy();
      if (this.props.on.end) {
        payload.source = 'end';
        this.props.on.end(payload);
      }
      return;
    }

    sessionStorage.setItem(this.props.sskey, nextInterval);
    this.timeoutRef = setTimeout(this.boundUpdate, this.props.interval);
  }

  clearCount() {
    sessionStorage.setItem(this.props.sskey, 0);
  }

  init() {
    this.boundUpdate();
  }

  stopCount() {
    clearInterval(this.timeoutRef);
  }

  destroyCounter() {
    this.stopCount();
    sessionStorage.removeItem(this.props.sskey);
  }

  // Getter
  get getProps() {
    return this.props;
  }

  mergeDeep(target, source) {
    function isObject(item) {
      return item && typeof item === 'object' && !Array.isArray(item) && item !== null;
    }

    if (isObject(target) && isObject(source)) {
      Object.keys(source).forEach(key => {
        if (isObject(source[key])) {
          if (!target[key] || !isObject(target[key])) {
            target[key] = source[key];
          }
          this.mergeDeep(target[key], source[key]);
        } else {
          Object.assign(target, { [key]: source[key] });
        }
      });
    }
    return target;
  }
}
