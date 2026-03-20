# This is self-contained widget.

By default all modals appear right above main footer

Alternatively place following code in desired location:
TimedAction plugin should be left within directory only if you dont have it in main bundle!

```php
<?php 
  // Side Links Widget
  get_template_part('template-parts/widget-modal-popout/snippet', 'markup'); 
?> 
```

## Tips

Some themes will not be setup to automate scropt bundling and SCSS compilation.

If that is the case:

use style.css in widget-modal-popout directory

widget-modal-popout-prod.js

```js
      wp_register_script('timed-modal', get_template_directory_uri() . '/modules/widget-modal-popout/widget-modal-popout-prod.js', array(), '1.0.0'); // Timed Modal
      wp_enqueue_script('timed-modal'); // Enqueue it!
```

Locations can differ between themes depending on where you place files.

If site settings are missing within theme - add this to functions.php


```php
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Site Settings',
		'menu_title'	=> 'Site Settings',
		'menu_slug' 	=> 'site-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}
```

It is very likely that older themes will not have **hm_get_template_part** function. If that is the case include code from **old_themes_get_template_part_with_args.php content in functions.php

