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