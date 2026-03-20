
<?php
acf_register_block(array(
  'name' => 'carousel', // this forms directory name. In this example = "gutenberg-carousel"
  'title' => __('Carousel'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block',
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'carousel', get_template_directory_uri() . '/assets/prod/gutenberg-carousel/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
    wp_enqueue_script( 'carousel', get_template_directory_uri() . '/assets/prod/gutenberg-carousel/module.js', ['km-core-scripts','km-bundle-scripts'], KM_MODULE_VERSION, true );
  },
));
