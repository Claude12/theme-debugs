
# Shortcode img_content

This shortcode is handy if you want to render simple card that contains image, title and small text.

## Options

1. **img** - optional. Absolute url to the image. ( select from media - fetch url and paste in )
2. **title** - optional. If set h3 will be added with particular content.
3. **classes** - optional. Any aditional classes to add to parent block.
4. **width** - optional. If set this value will be added as maximum width to the card. (pixels)
5. **img_alt** - optional. If set. this will be applied as alternative text to image. If not it will try to use title setting. If this is not used then it falls back to `illustrative image`
  

## Content

Anything between brackets is considered to be content and will be rendered below title. Defaults back to `Awaiting content`.


## Usage

```php
[img_content] CONTENT [/img_content]

[img_content img="absolute url to img" title="My title" classes="extra class names" width=1200 img_alt="My image"] Rest of the content [/img_content]

```