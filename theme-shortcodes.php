<?php

function ketchup_block( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-block/shortcode-block.php';
  return ob_get_clean();
}

add_shortcode( 'block', 'ketchup_block' );
?>
<?php

function ketchup_block_link( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-block-link/shortcode-link.php';
  return ob_get_clean();
}

add_shortcode( 'block_link', 'ketchup_block_link' );
?>
<?php

function ketchup_button( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-button/shortcode-button.php';
  return ob_get_clean();
}

add_shortcode( 'km_button', 'ketchup_button' );
?>
<?php
/**
 *  Usage in WYSIWYG
 *  [card argument=value]
 *   <a href="http://emintell-com.stackstaging.com/connect/">Connect</a>
 *   [/card]
 *  arguments are available in $atts array and content between shortcode tags in $content
 *  arguments are passed to php file within include
 */
function ketchup_card( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-card/shortcode-middleware.php';
  return ob_get_clean();
}

add_shortcode( 'card', 'ketchup_card' );
?>
<?php

function ketchup_img_content( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-img_content/shortcode-img_content.php';
  return ob_get_clean();
}

add_shortcode( 'img_content', 'ketchup_img_content' );
?>
<?php

function ketchup_google_directions( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-google-directions/shortcode-google-directions.php';
  return ob_get_clean();
}

add_shortcode( 'km_directions', 'ketchup_google_directions' );
?>
<?php


/**
 * 
 *  [km-cta-block modal="km-modal-all-number-4-at-10" label="My label"] Content[/km-cta-block]
 * 
 */
function ketchup_km_cta_block( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-km-cta-block/shortcode-km-cta-block.php';
  return ob_get_clean();
}

add_shortcode( 'km-cta-block', 'ketchup_km_cta_block' );
?>
<?php


/**
 * 
 *  [km-modal classes="cta-5 cta-button cta-hover-x" modal="km-modal-all-number-4-at-10"] My popup [/km-modal]
 * 
 * 
 */
function ketchup_km_modal( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-km-modal/shortcode-km-modal.php';
  return ob_get_clean();
}

add_shortcode( 'km-modal', 'ketchup_km_modal' );
?>
<?php

function ketchup_link( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-link/shortcode-link.php';
  return ob_get_clean();
}

add_shortcode( 'arrow_link', 'ketchup_link' );
?>
<?php

function ketchup_recent_posts( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-recent-posts/shortcode-recent-posts.php';
  return ob_get_clean();
}

add_shortcode( 'recent_posts', 'ketchup_recent_posts' );
?>