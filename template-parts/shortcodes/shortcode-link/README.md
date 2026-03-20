# Shortcode Link

Simple shortcode to render single link anywhere on the page.

## Usage

To use shortcode type following in WYSIWYG editor:

```php
  [link][/link]
```

## Configuration

Shortcode accepts several options
<!-- [button external=true url="https://www.google.co.uk" classes="car-links-bla two"]Link to google[/button] -->

1. **external** - boolean. `true` if external and `false` if internal ( defaults to false ). External receives `target_blank` and `rel=noopener`
2. **url**  - target page url link takes user to. (defaults to `#0`)
3. **classes** - ability to add extra class names to given link. defaults to null
  
## Content
  link content is text within square brackets.

  For example 

  ```php
  [link]My link title[/link]
  ```

## Full Link shortcode

  ```php
  [arrow_link external=true url="https://www.google.co.uk" classes="extra-class"]Link to google[/arrow_link]
  ```