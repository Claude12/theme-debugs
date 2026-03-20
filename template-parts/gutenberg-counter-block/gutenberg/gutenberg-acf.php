
<?php
acf_register_block(array(
  'name' => 'counter-block', // this forms directory name. In this example = "gutenberg-counter-block"
  'title' => __('Counter Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block',
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'counter-block', get_template_directory_uri() . '/assets/prod/gutenberg-counter-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
    wp_enqueue_script( 'counter-block', get_template_directory_uri() . '/assets/prod/gutenberg-counter-block/module.js', ['km-core-scripts','km-bundle-scripts'], KM_MODULE_VERSION, true );
  },
));
