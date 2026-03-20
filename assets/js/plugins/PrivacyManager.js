class PrivacyManager {
  constructor(userProps = {}) {
    this.defaults = {
      expDays: 'Thu, 01 Jan 1970 00:00:00 GMT', // used to set expiry for cookies
      saveAsCookie: true, // if true - cookies used instead of local storage
      cookieExp: 365, // days till expiry ( cookie only )
      savePreferences: true, // save set preferences in local storage or not?
      key: 'privacy-preferences', // key for local storage
      form: null, // DOM object
      on: {
        init: null,
        update: null,
        restore: null,
      },
      items: [], // {name = str, onApprove = func, onDeny = func, onAction = func checked=bool //default state}
    };
    this.props = this.mergeDeep(this.defaults, userProps);
    this.savedPreferences = [];

    // No point continuing if you dont have a form
    const form = this.props.form;
    if (!(form instanceof Element) || !form)
      throw new Error('"form" should be DOM object');

    // Bind this
    this.init = this.init.bind(this);
    this.updateFromSave = this.updateFromSave.bind(this);
    this.updatePreferences = this.updatePreferences.bind(this);
    this.updateSaved = this.updateSaved.bind(this);
    this.perfomAction = this.performAction.bind(this);
    this.restoreDefaults = this.restoreDefaults.bind(this);
    this.fetchCookies = this.fetchCookies.bind(this);
    this.addCookie = this.addCookie.bind(this);
    this.getCookie = this.getCookie.bind(this);
    this.deleteCookie = this.deleteCookie.bind(this);
    this.payload = this.payload.bind(this);

    this.formatted = this.fetchCookies();
    this.init();
  }

  init() {
    const savedCookie = this.getCookie(this.props.key);
    const savedStorage = localStorage.getItem(this.props.key);

    if (this.props.saveAsCookie) {
      this.savedPreferences = savedCookie
        ? JSON.parse(savedCookie.content)
        : [];

      if (savedStorage) localStorage.removeItem(this.props.key);
    }

    if (!this.props.saveAsCookie) {
      this.savedPreferences = savedStorage ? JSON.parse(savedStorage) : [];
      if (savedCookie) this.deleteCookie(this.props.key);
    }

    const form = this.props.form;
    form.addEventListener('submit', this.updatePreferences, false);

    const savedPref = this.savedPreferences;

    if (savedPref.length > 0) this.updateFromSave();
    this.updatePreferences();

    // init on.init
    this.payload(this.props.on.init);
  }

  updateFromSave() {
    const form = this.props.form;
    const saved = this.savedPreferences;

    saved.forEach((savedItem) => {
      let item = form[savedItem.name];
      if (item) {
        item.checked = savedItem.checked;
      }
    });
  }

  payload(func) {
    const isCookie = this.props.saveAsCookie;

    const data = isCookie
      ? this.getCookie(this.props.key)
      : localStorage.getItem(this.props.key);

    let result = null;

    if (data) result = isCookie ? data.content : data;

    const payload = {
      form: this.props.form,
      id: this.props.key,
      data: result ? JSON.parse(result) : [],
    };

    if (func) func(payload);
  }

  updatePreferences(e) {
    if (e) e.preventDefault();
    const form = this.props.form;
    this.savedPreferences = [];
    this.props.items.forEach((item) => {
      let name = item.name;
      if (name && form[name]) this.performAction(form[name].checked, item);
    });
    if (this.props.savePreferences) this.updateSaved();

    // init on update
    this.payload(this.props.on.update);
  }

  updateSaved() {
    const cookieExp = this.props.cookieExp;
    const key = this.props.key;

    if (this.props.saveAsCookie) {
      this.addCookie(key, JSON.stringify(this.savedPreferences), cookieExp);
    } else {
      localStorage.setItem(key, JSON.stringify(this.savedPreferences));
    }
  }

  performAction(checked, config) {
    const isChecked = checked || false;
    const onApprove = config.onApprove;
    const onDeny = config.onDeny;
    const name = config.name;

    const item = {
      name: name,
      checked: isChecked,
    };

    this.savedPreferences.push(item);

    if (isChecked) {
      if (onApprove) onApprove(item);
    } else {
      if (onDeny) onDeny(item);
    }

    if (config.onAction) config.onAction(item);
  }

  restoreDefaults() {
    const form = this.props.form;
    const items = this.props.items;

    if (this.props.saveAsCookie) {
      this.deleteCookie(this.props.key);
    } else {
      localStorage.removeItem(this.props.key);
    }

    const preferences = [];

    items.forEach((item) => {
      let checkbox = form[item.name];
      if (checkbox) {
        let output = {
          name: item.name,
          checked: item.checked,
        };
        preferences.push(output);
        checkbox.checked = item.checked;
        this.performAction(form[item.name].checked, item);
      }
    });

    this.savedPreferences = preferences;

    if (this.props.savePreferences) this.updateSaved();

    // init on restore
    this.payload(this.props.on.restore);
  }

  fetchCookies() {
    let cookies = document.cookie.split(';');
    let result = [];

    cookies.forEach((cookie) => {
      let src = cookie.trim();

      // Name
      let cookieName = src.split('=')[0];

      // Content
      let cookieContent = src.split('=');
      cookieContent.shift();
      cookieContent = cookieContent.join('=');

      // let cookie formatting
      let formatted = {
        name: cookieName,
        content: cookieContent,
      };

      result.push(formatted);
    });

    return result;
  }

  addCookie(name, content, expDays = 365, props = '') {
    if (!name || !content) return this;

    const d = new Date();
    d.setTime(d.getTime() + expDays * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${content};expires=${d.toUTCString()};${props}path=/`;

    this.formatted = this.fetchCookies();

    return this;
  }

  getCookie(name = '') {
    const arr = this.formatted;
    const result = arr.filter((item) => item.name === name);

    if (result.length === 0) return null;

    return result[0];
  }

  deleteCookie(name = '') {
    const target = this.getCookie(name);
    if (!target) return null;
    document.cookie = `${target.name}='';expires=${this.props.expDays};path=/`;
    this.formatted = this.fetchCookies();
    return target;
  }

  // Getter
  get getProps() {
    return this.props;
  }

  mergeDeep(target, source) {
    function isObject(item) {
      return (
        item &&
        typeof item === 'object' &&
        !Array.isArray(item) &&
        item !== null
      );
    }

    if (isObject(target) && isObject(source)) {
      Object.keys(source).forEach((key) => {
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

/**

Privacy manager is a plugin that allows to control **[cookies](https://en.wikipedia.org/wiki/HTTP_cookie)** or **[web storage](https://en.wikipedia.org/wiki/Web_storage)** data on client browser to ensure functionality of the site or enhance user experience.

## Cookie vs web storage?

Privacy Manager plugin allows us to choose which option to use. Cookie or web storage. This is due to their differences. Suh as those listed below:

### Cookie

- Cookie information is sent to server via headers
- Cookie with _HttpOnly_ flag are not available in Javascript, thus is immune to XSS attacks (CSRF still applies)
- Cookie can have expiry.
- All cookies are stored in single string

### Web storage ( local storage )

- Accessible from javascript always.
- Removal has to happen manually ( no expiry )
- Open to XSS attacks
- Larger capacity ( around 5mb )

Discussion regarding differences worth reading **[here](https://stackoverflow.com/questions/3220660/local-storage-vs-cookies)**.

## Approach

Plugin allows to store bulk information about user choices in JSON string in web storage or, if chosen, as cookie. JSON format remains the same.

Keeping them the same in terms of formatting is intentional and allows quick toggle of storage used.

Both - **key** and **name** can be replaced in props object to suit your needs.

## Setup

Plugin relies on HTML and JS working together. Particular HTML form and Javascript object with properties to run instance of the plugin.

Example HTML form:

```html
<section class="privacy-manager">
  <div class="pm-wrap">
    <h2 class="pm-heading">Privacy Manager</h2>

    <form class="pm-form" id="privacy-manager">
      <!-- Mandatory Functional cookies -->
      <div>
        <label for="functional">Functional</label>
        <input type="checkbox" name="functional" checked />
      </div>

      <!-- Mandatory Analytical cookies -->
      <div>
        <label for="analytical">Analytical</label>
        <input type="checkbox" name="analytical" checked />
      </div>

      <!-- Targeting cookies -->
      <div>
        <label for="targeting">Targeting</label>
        <input type="checkbox" name="targeting" />
      </div>

      <input type="submit" value="Update preferences" />
    </form>
  </div>
</section>
```

Each input field has a name which represents given item or item set (The way you control it is entirely up to you).

Instance using options/form above would be initialized as follows:

```js
const privacyManager = new PrivacyManager({
  form: document.getElementById('privacy-manager'),
  items: [
    {
      name: 'functional',
      checked: true,
    },
    {
      name: 'analytical',
      checked: true,
    },
    {
      name: 'targeting',
      checked: false,
    },
  ],
});
```

Each item or item group is one element in **items** array. It will always contain `name` and `checked` keys. Name is a link between JS and HTML and boolean of `checked` is default value if restore defaults functionality is implemented.

HTML:

```html
<div>
  <label for="analytical">Analytical</label>
  <input type="checkbox" name="analytical" checked />
</div>
```

JS:

```js
{
  name: 'analytical',
  checked: true,
}
```

Above means that each item or item group should be added in both places.

## Methods

Plugin provides '**_on_**' methods as well as item/item group specific methods.

### "on"

The "on" key allows to hook into certain action within plugin. This can be useful if you need to reload window on update or notify user about changes.

Each method gets the same payload.

Payload includes:

- **form** : Form used for options ( DOM object )
- **id** : key/name used for cookie/web storage ( String )
- **data** : JSON parsed data with latest choices ( Array )

```js
{
 data: [
         {name: "functional", checked: true}
         {name: "analytical", checked: true}
         {name: "targeting", checked: false}
       ],
 form: form#privacy-manager.pm-form
 id: "privacy-preferences"
}
```

Sample in props object:

```js
  on: {
    init: (payload) => console.log(payload), // plugin initialization
    update: (payload) => console.log(payload), // cookie/web storage updated
    restore: (payload) => console.log(payload) // restore initiated
  },
```

Restore can be executed using restoreDefaults method available in plugin.

Sample:

```js
const restore = document.querySelector('.restore');
restore.addEventListener('click', privacyManager.restoreDefaults, false);
```

### Item specific methods

Items, beside name and default value, accept methods to be executed in different scenarios.

Each item in **items** can have  up to 3 hook methods :

- **onApprove** - executed if checkbox is checked.
- **onDeny** - executed if checkbox is un checked
- **onAction** - executed regardless of checkbox state.

Here we have a choice - Either to allow plugin transfer items to relevant method ( approve or deny ) or contain logic in your own method by executing **onAction**

Each method receives payload containing **name** and **checked** status:

```js
{
  name: 'targeting',
  checked: false
}
```

Full sample:

```js
    items: [
      {
        name: 'functional',
        onAction: (item) => console.log('Action', item),
        checked: true, // default value
      },
      {
        name: 'analytical',
        onApprove: (item) => console.log('Approve', item),
        onDeny: (item) => console.log('Deny', item),
        checked: true, // default value
      },
      {
        name: 'targeting',
        onApprove: (item) => console.log('Approve', item),
        onDeny: (item) => console.log('Deny', item),
        checked: false, // default value
      },
    ],
```

## Configuration object

Defaults:

```js
{
  expDays: 'Thu, 01 Jan 1970 00:00:00 GMT', // used to set expiry for cookies
  saveAsCookie: true, // if true - cookies used instead of local storage
  cookieExp: 365, // days till expiry ( cookie only )
  savePreferences: true, // save set preferences in local storage/cookie or not?
  key: 'privacy-preferences', // key for local storage
  form: null, // DOM object
  on: {
    init: null,
    update: null,
    restore: null,
  },
  items: [], // {name = str, onApprove = func, onDeny = func, onAction = func checked=bool //default state}
};
```

Single item (object inside items array):

```js
{
  name: 'targeting', // same as form input(checkbox) name
  onApprove: (item) => console.log('Approve', item), // if checked
  onDeny: (item) => console.log('Deny', item), // if denied
  onAction: (item) => console.log('Action', item), // regardless of approved/denied
  checked: false, // default value of checkbox (used on restore defaults)
}
```

Now example with usage:

```js
const privacyManager = new PrivacyManager({
  saveAsCookie: false,
  form: document.getElementById('privacy-manager'),
  on: {
    init: (payload) => console.log(payload),
    update: (payload) => console.log(payload),
    restore: (payload) => console.log(payload),
  },
  items: [
    {
      name: 'functional',
      onAction: (item) => console.log('Action', item),
      checked: true,
    },
    {
      name: 'analytical',
      onApprove: (item) => console.log('Approve', item),
      onDeny: (item) => console.log('Deny', item),
      checked: true,
    },
    {
      name: 'targeting',
      onApprove: (item) => console.log('Approve', item),
      onDeny: (item) => console.log('Deny', item),
      checked: false,
    },
  ],
});
```


**/
