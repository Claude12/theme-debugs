# Shortcode block

Intended use: wrap elements with common parent.

End result

```html
  <article class="shortcode-block property">
    <div class="sb-wrapper" style="max-width: property;">
    <!-- Anything between opening and closing brackets -->
    </div>
  </article>
```

## Properties

1. **classes** - classes added to the parent block.
2. **width** - maximum width of inner wrap. Useful to cap content width.
3. **screen_readers** - If set - screen reader only heading will be applied.


## Usage

```php
[block classes="class names" width=maximum_width_in_pixels screen_readers="optional screen reader title"] YOUR CONTENT [/block]
```