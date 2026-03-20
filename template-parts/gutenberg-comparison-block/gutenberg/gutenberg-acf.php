
<?php
acf_register_block(array(
  'name' => 'comparison-block', // this forms directory name. In this example = "gutenberg-comparison-block"
  'title' => __('Comparison Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block',
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'comparison-block', get_template_directory_uri() .'/assets/prod/gutenberg-comparison-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
  },
));
