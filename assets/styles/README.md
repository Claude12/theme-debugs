# Styles directory

<!-- @import "[TOC]" {cmd="toc" depthFrom=1 depthTo=6 orderedList=false} -->

<!-- code_chunk_output -->

- [Styles directory](#styles-directory)
  - [Directories](#directories)
    - [Core](#core)
    - [Theme](#theme)
    - [Fonts](#fonts)
    - [Colours.json](#coloursjson)
      - [gulp](#gulp)
        - [\_colours.scss file](#_coloursscss-file)
        - [colours.php file](#coloursphp-file)

<!-- /code_chunk_output -->

Includes bundles and site wide styling for the site.

Gulp build two bundles:

**km-bundle.min.css** contains all css and scss files within **template-parts/\*\*/\*** and **assets/styles/theme/\***

**km-core.min.css** contains all css and scss files within **./assets/styles/core/\*.{css,scss,min.css}**

## Directories

### Core

Lists core site wide styling. This includes reset and normalize as well as stylesheets from assets/js/libraries. These should be imported via an _import_

### Theme

Styling that is not part of the core, but is needed site wide. This includes common styling across multiple modules. Intended use for _\_.variables.scss_ is to provide an option to store colours and use them across modules. It is important to note that styles in **theme** ar appended at the bottom of the bundle after all styling from template parts is gathered. This means that anything you place there - wil override corresponding style rule in module stylesheet.

### Fonts

**fonts.php** is included within given directory because it is to be considered as part of the styles.

Think of this file as a middleware for enqueue_scripts. This file should be used to load any bespoke fonts into the site. It supports Typekit as well as google fonts

```php
function loadFonts(){
  return [
    'google-font' => '//fonts.googleapis.com/css?family=Roboto',
    'typekit-font' => '//use.typekit.net/lah1rix.css'
  ];
}
```

Above is example content for this file. Key is used to populate id for the stylesheet within the **head** tag and value is actual link to font. There is no limit for amount of fonts supported, however it is worth to note that less is better.

### Colours.json

This file contains theme colours. Any colour you want to be available in Picker field (ACF) and any colour you want to have dynamic class names created should be added in this file:

```json
{
  "colours": [
    {
      "value": "#000",
      "label": "Black",
      "slug": "black"
    }
  ]
}
```

Single colour consists of three fields.

1. value
2. label
3. slug

GULP builds ACF and CSS class names automatically on each "npm start"! Note, that if colours.json is modified - you need to restart gulp. This is because colour creation requires file reads and file recreatings.

#### gulp

Gulp does multiple things.

##### \_colours.scss file

\_colours.scss and colours.php can be created with command `node gulp-colours`. This command removes existing files if such exists and create php and scss file with data from colours.json. General rule : if you added colour in colours.json - run this command.

Sample content of given file:

```scss
// Do not modify this file. Add colours in colours.json and class name modification in gulp-colours.js! Restart gulp if colours.json is modified. Gulp is not watching this file.
$colours: (#000, Black, black), (#173f4f, Dark blue, dark-blue),
  (#20639b, Blue, blue), (#3caea3, Green, green), (#f6d55c, Yellow, yellow), (#ed553b, Orange, orange);

@each $value, $label, $slug in $colours {
  .has-#{$slug}-background-color {
    background-color: $value;
  }
  .has-#{$slug}-color {
    color: $value;
  }
  .has-#{$slug}-border-color {
    border-color: $value;
  }
  .has-#{$slug}-fill {
    fill: $value;
  }
}
```

Sample of compiled CSS:

```css
.has-teal-background-color {
  background-color: teal;
}
.has-teal-color {
  color: teal;
}
.has-teal-border-color {
  border-color: teal;
}
.has-teal-fill {
  fill: teal;
}
...
```

CSS style gulp task then re creates bundle.min.css with all the class names for each colour options set in colours.json. This means that adding relevant class would call relevant style property if cascading is correct. \_colours.scss in imported as last element in style.scss. This is necessary to ensure property override.

##### colours.php file

Automated with command : `node gulp-colours`

Typical layout of content:

```
#000 : Black : black
#173f4f : Dark blue : dark-blue
#20639b : Blue : blue
#3caea3 : Green : green
#f6d55c : Yellow : yellow
#ed553b : Orange : orange
```

which is the same as the one described in ACF picker field choice field. This file can then be used in "Add colours from theme" in ACF picker. End outcome is colour picker with already pre build class names to be used within theme.
