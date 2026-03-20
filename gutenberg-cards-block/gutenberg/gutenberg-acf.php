
<?php
acf_register_block(array(
  'name' => 'cards-block',
  'title' => __('Cards Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style('cards-block', get_template_directory_uri() . '/assets/prod/gutenberg-cards-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION );
  }
));
