<?php

require get_template_directory().'/assets/styles/fonts.php'; // Contains google fonts and typekit fonts


function load_custom_fonts(){
  $fonts = loadFonts();
  foreach ($fonts as $title => $link) {
    ?>
      <link id="<?php echo $title; ?>" rel="stylesheet" href="<?php echo $link; ?>">
    <?php
  }
}

function km_common_scripts(){
 
  $isDev = defined('KM_ENV') ? KM_ENV === 'dev' : 'live';

  
  $blocks = parse_blocks( get_the_content() );

  foreach($blocks as $block) {

    if(empty($block['blockName'])) continue;

    // Ignore all blocks that are not ACF. This includes nested or reusable blocks that are ACF. This issue is solved by adding enqueu_style whilst setting up Gutenberg also.
    if(strpos($block['blockName'], 'acf/') === false) continue;

   $blockName =  str_replace('acf/', 'gutenberg-', $block['blockName']);
   $fileLocation =  '/assets/prod/' . $blockName . '/module.css';

   if(file_exists(get_theme_file_path() . $fileLocation)) {
      wp_enqueue_style(str_replace('acf/', '', $block['blockName']), get_template_directory_uri() . $fileLocation, ['km-core-styles','km-bundle-styles'] , KM_MODULE_VERSION);
    }
    
  }

  // GSAP is loaded via CDN to ensure maximum cache
  wp_enqueue_script('gsap-core', '//cdnjs.cloudflare.com/ajax/libs/gsap/3.5.1/gsap.min.js', array(), $isDev ? microtime() : '3.5.1', true);
  wp_enqueue_script('gsap-scrolltrigger', '//cdnjs.cloudflare.com/ajax/libs/gsap/3.5.1/ScrollTrigger.min.js', array('gsap-core'), $isDev ? microtime() : '3.5.1', true);

  // Available on both admin side and client side!	
  wp_enqueue_script('km-core-scripts', get_template_directory_uri() . '/assets/js/km-core.min.js', array(), $isDev ? microtime() : '1.0.0', true);
  wp_enqueue_script('km-bundle-scripts', get_template_directory_uri() . '/assets/js/km-bundle.min.js', array('km-core-scripts'), $isDev ? microtime() : '1.0.0', true);
  wp_enqueue_style('km-core-styles', get_template_directory_uri() . '/assets/styles/km-core.min.css', array(), $isDev ? microtime() : '1.1.0' , false); 
  wp_enqueue_style('km-bundle-styles', get_template_directory_uri() . '/assets/styles/km-bundle.min.css', array('km-core-styles'), $isDev ? microtime() : '1.1.0', false);	

  wp_enqueue_style('km-colours', get_template_directory_uri() . '/assets/styles/colours.css', ['km-core-styles','km-bundle-styles'] , $isDev ? microtime() : '1.0.0');
  wp_enqueue_style('km-site-builder-colours', get_template_directory_uri() . '/assets/styles/km-site-builder-colours.css', ['km-core-styles','km-bundle-styles'] , $isDev ? microtime() : '1.0.0');	

  // Wedding planner
  if ( is_page_template( 'template-book-now.php' ) ||  is_page('estimated-total') ) {
    wp_enqueue_style('km-wedding-planner-fancybox', get_template_directory_uri() . '/custom/fancybox/jquery.fancybox.css', ['km-core-styles','km-bundle-styles'] , $isDev ? microtime() : '1.0.0');
    wp_enqueue_style('km-wedding-planner', get_template_directory_uri() . '/custom/km-wedding-planner.min.css', ['km-core-styles','km-bundle-styles'] , $isDev ? microtime() : '1.0.0');

    //wp_enqueue_script('km-wedding-planner-fancy', get_template_directory_uri() . '/custom/fancybox/jquery.fancybox.pack.js', array('jquery'), $isDev ? microtime() : '1.0.0', true); included in plugins.js?

    wp_enqueue_script('km-wedding-planner-plugins', get_template_directory_uri() . '/custom/plugins.js', array('km-core-scripts','km-bundle-scripts','jquery'), $isDev ? microtime() : '1.0.0', true);
    wp_enqueue_script('km-wedding-planner', get_template_directory_uri() . '/custom/planner.js', array('km-core-scripts','km-bundle-scripts','jquery','km-wedding-planner-plugins'), $isDev ? microtime() : '1.0.0', true);
  } 
}


function km_unicorn_scripts() {

  // JAVASCRIPT
  wp_enqueue_script('jquery');

  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
      wp_enqueue_script( 'comment-reply' );
  }

}

  function admin_footer_data(){
    echo '<svg class="svg-set" xmlns="http://www.w3.org/2000/svg" style="display: none;">';
    require get_template_directory() . '/assets/svg-icons/svg-set.php'; 
    echo '</svg>';
  }   


//Admin-ajax
function km_unicorn_admin_URL() { 
    $MyTemplatepath = get_template_directory_uri(); 
    $MyHomepath = esc_url( home_url( '/' ) ); 
    $admin_URL = admin_url( 'admin-ajax.php' ); // Your File Path
    return array(
        'admin_URL' =>  $admin_URL,
        'MyTemplatepath' =>  $MyTemplatepath,
        'MyHomepath' =>  $MyHomepath
    );
}
