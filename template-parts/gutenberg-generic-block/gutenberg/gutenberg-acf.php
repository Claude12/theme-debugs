
<?php
acf_register_block(array(
  'name' => 'generic-block',
  'title' => __('Generic Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
   'enqueue_assets' => function(){
    wp_enqueue_style( 'generic-block', get_template_directory_uri() . '/assets/prod/gutenberg-generic-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
  },
));
