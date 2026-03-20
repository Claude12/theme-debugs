<?php

function ketchup_button( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-button/shortcode-button.php';
  return ob_get_clean();
}

add_shortcode( 'km_button', 'ketchup_button' );
?>