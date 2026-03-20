
<?php
acf_register_block(array(
  'name' => 'post-type-filter', // this forms directory name. In this example = "gutenberg-post-type-filter"
  'title' => __('Post Type Filter'),
  'description' => __('Works within blog page.'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block',
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'post-type-filter', get_template_directory_uri() . '/assets/prod/gutenberg-post-type-filter/module.css', ['km-core-styles','km-bundle-styles'],'' );
    wp_enqueue_script( 'post-type-filter', get_template_directory_uri() . '/assets/prod/gutenberg-post-type-filter/module.js', ['km-core-scripts','km-bundle-scripts'], '', true );
  },
));
