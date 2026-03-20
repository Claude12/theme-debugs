
<?php
acf_register_block(array(
  'name' => 'form-block', // this forms directory name. In this example = "gutenberg-form-block"
  'title' => __('Form Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'form-block', get_template_directory_uri() . '/assets/prod/gutenberg-form-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
  },
));
