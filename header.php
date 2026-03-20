<?php 
  global $post;
?>

<!doctype html>
  <html <?php language_attributes(); ?>>
    <head>
      <meta charset="<?php bloginfo( 'charset' ); ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
      <link rel="profile" href="https://gmpg.org/xfn/11">
      <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />
      <?php include(TEMPLATEPATH . "/third-party-services/google-tag-manager-head.php"); ?>
      <?php wp_head(); ?>

    </head>

    <?php $envClass = get_field('environment','option')['notify_users'] && is_user_logged_in() ? 'km-env-notification-active' : null; ?>
    <style>
      .top-bar .cta-set .cta-7,
      .push-menu-wrap .cta-set .cta-7 {
        display: none;
      } 
    </style>
    <body <?php body_class($envClass); ?>>
	
    <?php include(TEMPLATEPATH . "/third-party-services/google-tag-manager-body.php"); ?>

    <?php
      // Clearing Cookies via JS due to ACF unavailability to do this with PHP
      $acceptAnalytics = getPrivacyPref('analytical') !== null ? getPrivacyPref('analytical') : true;
      $gtmID = get_field('google_tag_manager_id','option') ?: null;
      $gtmStaticCookies = ['_ga','_gcl_au','_gid']; // static known cookie names
      $gtmCookies = get_field('google_tag_cookie_names','option') ? explode(',', get_field('google_tag_cookie_names','option')) : []; // ability to add extra.

      if(!$acceptAnalytics && $gtmID) {
         echo '<span id="km-gtm-cookie-ref" style="display:none;">' . implode(',',array_merge($gtmStaticCookies, $gtmCookies)) . '</span>'; 
      }

    ?>
    <?php get_template_part('template-parts/theme-environment/snippet', 'markup');  ?>

    <div class="container">
    
      <?php get_template_part('template-parts/top-bar/snippet', 'markup'); ?>
    
      <h1 class="sr-only"><?php echo yoastVariableToTitle($post->ID); ?></h1>

      <svg class="svg-set" xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <?php get_template_part('/assets/svg-icons/svg','set'); ?>
      </svg>

      <?php 

        get_template_part('template-parts/privacy-manager/snippet', 'markup');
        get_template_part('template-parts/widget-side-links/snippet', 'markup'); 
      

        $blocks = parse_blocks( get_the_content() );
        $heroExcludes = get_field('disable_hero_carousel','options') ?: [];
        $heroExists = false;
  

     
        foreach ( $blocks as $block ) {
          if ( 'acf/hero-carousel' === $block['blockName'] ) {
            $heroExists = true;
            break;
          } 
        }

        if(!is_search() && !in_array(km_get_page_id(), $heroExcludes)){
          if($heroExists){
            echo renderBlocks('acf/hero-carousel');
          }else {
            get_template_part('template-parts/gutenberg-hero-carousel/snippet', 'markup');
          }
        }

        // Render carousel outside main-content only if it is set as first child.
        if(!empty($blocks) && $blocks[0]['blockName'] === 'acf/carousel') {
          echo render_block($blocks[0]);
        }