
<?php
acf_register_block(array(
  'name' => 'site-info', // this forms directory name. In this example = "gutenberg-site-info"
  'title' => __('Site Info'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
));
