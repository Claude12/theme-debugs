<?php

function ketchup_link( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-link/shortcode-link.php';
  return ob_get_clean();
}

add_shortcode( 'arrow_link', 'ketchup_link' );
?>