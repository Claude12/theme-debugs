<?php
      function my_acf_init() {


acf_register_block(array(
  'name' => 'blog-feed', 
  'title' => __('Blog Feed'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'blog-feed', get_template_directory_uri() . '/assets/prod/gutenberg-blog-feed/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
  },
));


acf_register_block(array(
  'name' => 'card-content',
  'title' => __('Card Content'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_style' => get_template_directory_uri() . '/assets/prod/gutenberg-card-content/module.css',
));


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


acf_register_block(array(
  'name' => 'cta-strip',
  'title' => __('Cta Strip'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'cta-strip', get_template_directory_uri() . '/assets/prod/gutenberg-cta-strip/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION );
    wp_enqueue_script( 'cta-strip', get_template_directory_uri() . '/assets/prod/gutenberg-cta-strip/module.js', ['km-core-scripts','km-bundle-scripts'], KM_MODULE_VERSION, true );
  },
));


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


acf_register_block(array(
  'name' => 'google-map', 
  'title' => __('Google Map'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'google-map', get_template_directory_uri() . '/assets/prod/gutenberg-google-map/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
    wp_enqueue_script( 'google-map', get_template_directory_uri() . '/assets/prod/gutenberg-google-map/module.js', ['km-core-scripts','km-bundle-scripts'], KM_MODULE_VERSION, true );
  },
));


acf_register_block(array(
  'name' => 'hero-carousel',
  'title' => __('Hero Carousel'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
));


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


acf_register_block(array(
  'name' => 'media-block', // this forms directory name. In this example = "gutenberg-media-block"
  'title' => __('Media Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block',
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style('media-block', get_template_directory_uri() . '/assets/prod/gutenberg-media-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION );
    wp_enqueue_script('media-block', get_template_directory_uri() . '/assets/prod/gutenberg-media-block/module.js', ['km-core-scripts','km-bundle-scripts'], KM_MODULE_VERSION, true );
  }
));


acf_register_block(array(
  'name' => 'panel-block',
  'title' => __('Panel Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style('panel-block', get_template_directory_uri() . '/assets/prod/gutenberg-panel-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION );
    wp_enqueue_script('panel-block', get_template_directory_uri() . '/assets/prod/gutenberg-panel-block/module.js', ['km-core-scripts','km-bundle-scripts'], KM_MODULE_VERSION, true );
  }
));


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


acf_register_block(array(
  'name' => 'sibling-cards',
  'title' => __('Sibling Cards'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'sibling-cards', get_template_directory_uri() . '/assets/prod/gutenberg-sibling-cards/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
  },
));


acf_register_block(array(
  'name' => 'site-info', // this forms directory name. In this example = "gutenberg-site-info"
  'title' => __('Site Info'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
));


acf_register_block(array(
  'name' => 'spacer-block', 
  'title' => __('Spacer Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'spacer-block', get_template_directory_uri() . '/assets/prod/gutenberg-spacer-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION);
  },
));


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


acf_register_block(array(
  'name' => 'testimonial-block',
  'title' => __('Testimonial Block'),
  'description' => __(''),
  'render_callback'	=> 'ketchup_gutenberg_block', // Custom function. This will render correct markup
  'category' => 'ketchup-modules',
  'mode' => 'edit',
  'icon' => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : 'admin-tools',
  'keywords' => array(),
  'enqueue_assets' => function(){
    wp_enqueue_style( 'testimonial-block', get_template_directory_uri() . '/assets/prod/gutenberg-testimonial-block/module.css', ['km-core-styles','km-bundle-styles'], KM_MODULE_VERSION );
    wp_enqueue_script( 'testimonial-block', get_template_directory_uri() . '/assets/prod/gutenberg-testimonial-block/module.js', ['km-core-scripts','km-bundle-scripts'], KM_MODULE_VERSION, true );
  },
));

}