
<?php
acf_register_block(array(
  'name' => 'links-strip', // this forms directory name. In this example = "gutenberg-links-strip"
  'title' => __('Links Strip'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'links-strip', get_template_directory_uri() . '/assets/prod/gutenberg-links-strip/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION );
  },
));
