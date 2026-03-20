
<?php
acf_register_block(array(
  'name' => 'get-in-touch',
  'title' => __('Get in touch'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'get-in-touch', get_template_directory_uri() . '/assets/prod/gutenberg-get-in-touch/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
    wp_enqueue_script( 'get-in-touch', get_template_directory_uri() . '/assets/prod/gutenberg-get-in-touch/module.js', ['km-core-scripts','km-bundle-scripts'], KM_MODULE_VERSION, true );
  },
));
