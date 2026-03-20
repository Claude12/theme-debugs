
<?php
acf_register_block(array(
  'name' => 'media-block', // this forms directory name. In this example = "gutenberg-media-block"
  'title' => __('Media Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block',
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style('media-block', get_template_directory_uri() . '/assets/prod/gutenberg-media-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION );
    wp_enqueue_script('media-block', get_template_directory_uri() . '/assets/prod/gutenberg-media-block/module.js', ['km-core-scripts','km-bundle-scripts'], KM_MODULE_VERSION, true );
  }
));
