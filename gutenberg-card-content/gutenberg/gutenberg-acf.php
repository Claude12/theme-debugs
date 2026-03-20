
<?php
acf_register_block(array(
  'name' => 'card-content',
  'title' => __('Card Content'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_style' => get_template_directory_uri() . '/assets/prod/gutenberg-card-content/module.css',
));
