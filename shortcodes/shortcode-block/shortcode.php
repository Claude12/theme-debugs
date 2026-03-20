<?php

function ketchup_block( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-block/shortcode-block.php';
  return ob_get_clean();
}

add_shortcode( 'block', 'ketchup_block' );
?>