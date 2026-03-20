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