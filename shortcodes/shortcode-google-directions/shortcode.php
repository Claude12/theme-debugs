<?php

function ketchup_google_directions( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-google-directions/shortcode-google-directions.php';
  return ob_get_clean();
}

add_shortcode( 'km_directions', 'ketchup_google_directions' );
?>