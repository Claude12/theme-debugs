
<?php
acf_register_block(array(
  'name' => 'split-content-section',
  'title' => __('Split Content Section'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'split-content-section', get_template_directory_uri() . '/assets/prod/gutenberg-split-content-section/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
  },
));
