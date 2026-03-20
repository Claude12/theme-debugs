
<?php
acf_register_block(array(
  'name' => 'latest-news-item', // this forms directory name. In this example = "gutenberg-heading"
  'title' => __('Latest News Item'),
  'description' => __('Description for Latest News Item.'),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'latest-news-item', get_template_directory_uri() . '/assets/prod/gutenberg-latest-news-item/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
  },
));
