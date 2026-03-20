
# Assets directory

<!-- TOC -->
- [Assets directory](#assets-directory)
  - [Why 2 bundles?](#why-2-bundles)
  - [Directories](#directories)
    - [Helpers](#helpers)
    - [Libraries](#libraries)
    - [Plugins](#plugins)
    - [Polyfills](#polyfills)
    - [Theme](#theme)
    - [tmp](#tmp)


This directrory should contain any data connected to sitewide ( global ) Javascript.

Outcome of all files within directories apart from directory called **theme** will be compiled in **km-core.min.js**

Outcome of **theme** along with **/template-parts/\*\*.{js,min.js}**
will be compiled in **km-bundle.min.js**

## Why 2 bundles?

Couple of reasons. Main bundle contains stuff that is unlikely to change and should be used in all sites, in contrast theme bundle contains site specific javascript. This will reduce ( speed up ) gulp tasks and help with debugging.

## Directories

Legend of all directories within js directory.

### Helpers

 Small reusable functions that can be used across the site. ES6 can be used in these files as they are being compiled via Babel

### Libraries

Libraries should be separated by directories because library might contain not only JS files. pattern for gulp **/assets/js/libraries/library-name/\*\.{js,min.js}**

Files within this directory are simple concatinated to the core bundle. Please ensure that JS is compatable with supported browsers.

### Plugins

Plugins written by Ketchup. Reusable code that performs full action. For example - **KetchupSwiper.js** is middleware for Swiper plugin and it controls all swiper instances across the site, in contrast - **KetchupMenu.js** - is responsible for site main menu.

Files within this directory are run via babel. ES6 is supported.

### Polyfills

Polyfills needed for backwards compatability for ES6. Run via babel

### Theme

All js files will be compiled into km-bundle.js along with any js found within template-parts js. Sometimes you JS does nto apply to any module and should be applied site wide. This is the folder to use.

### tmp

Directory with README.md placeholder file. This direcotry is used in gulp and stores core.js and libs.js until further processed. Developer should not put anything here. README.md is left so that you could push this directory to Github

