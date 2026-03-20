class Cookie {
  constructor() {
    this.expDays = 'Thu, 01 Jan 1970 00:00:00 GMT';

    // Bind this
    this.fetch = this.fetch.bind(this);
    this.add = this.add.bind(this);
    this.get = this.get.bind(this);
    this.delete = this.delete.bind(this);

    this.formatted = this.fetch();
  }

  fetch() {
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

  add(name, content, expDays = 365, props = '') {
    if (!name || !content) return this;

    const d = new Date();
    d.setTime(d.getTime() + expDays * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${content};expires=${d.toUTCString()};${props}path=/`;

    this.formatted = this.fetch();

    return this;
  }

  get(name = '') {
    const arr = this.formatted;
    const result = arr.filter((item) => item.name === name);

    if (result.length === 0) return null;

    return result[0];
  }

  delete(name = '', domain = window.location.hostname) {
    const target = this.get(name);
    if (!target) return null;
    document.cookie = `${target.name}='';expires=${this.expDays};path=/;domain=${domain};`;
    this.formatted = this.fetch();
    return target;
  }
}

/**
Cookies
Simple class to make CRUD operations easier for cookies. Once class is initiated, its constructor calls function to fetch document.cookie and formats each cookie by name and value.

For example:

Default cookie string:

"demo-cookie=new content; with-props=my cookiewith props; wp-settings-1=libraryContent%3Dbrowse%26editor%3Dtinymce%26mfold%3Do; wp-settings-time-1=1591872952"

Formatted version with Cookie class

[
 {name: "demo-cookie", content: "new content"}
 {name: "with-props", content: "my cookiewith props"}
 {name: "wp-settings-1", content: "libraryContent%3Dbrowse%26editor%3Dtinymce%26mfold%3Do"}
 {name: "wp-settings-time-1", content: "1591872952"}
]

1. Methods
There are 4 methods within this class. fetch, add, get, delete. Update method was not created as we are able to modify any cookie by using add method, provided, you use the same cookie name.


1.1. Fetch
Initialisation:

const cookie = new Cookie();
cookie.fetch(); // returns array with formatted cookies.
Method is used in class constructor to populate data. This could also be called if you update cookie. This means that you would need to re-fetch cookies in class

const cookie = new Cookie();
cookie.formatted = cookie.fetch();
Above is useful only if you perform actions without page re-load and want to keep class data in sync.


1.2. Add
Initialisation:

const cookie = new Cookie();
cookie.add('my-cookie', 'my-content', 20, 'SameSite=Strict;'); // Returns class itself
OR

// Save cookie for 365 days.
const cookie = new Cookie();
cookie.add('my-cookie-1', 'my-content=1'); // Returns class itself
Method used to add cookie with optional time in days of expiration and any extra properties such as sameSite. Only mandatory arguments are first two - cookie name and its content. method will return class instance if one of above is missing.

Default arguments

Argument 3 - 365 ( expiration in days ) Argument 4 - empty string ( no extra flags )

This method is also to be used to modify cookie. Simply use the same cookie name. Re-fetching cookies is not required after adding. This is done internally.


1.3. Get
Initialisation:

const cookie = new Cookie();
cookie.get('my-cookie'); // Returns object of cookie
Example of return value:

{
  name: "my-cookie",
  content: "my cookie content"
}
Use cookie.get('my-cookie').content if only content is needed.

Method will return null if cookie does not exist.


1.4. Delete
Initialisation:

const cookie = new Cookie();
cookie.delete('my-cookie', 'www.google.co.uk); // Returns deleted cookie . cookie name, cookie domain - defaults to current host name
Method for deleting cookies. This returns deleted item. Deletion is achieved by modifying expiration date to: Thu, 01 Jan 1970 00:00:00 GMT
 */
