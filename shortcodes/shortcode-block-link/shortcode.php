<?php

function ketchup_block_link( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-block-link/shortcode-link.php';
  return ob_get_clean();
}

add_shortcode( 'block_link', 'ketchup_block_link' );
?>