<?php

function ketchup_img_content( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-img_content/shortcode-img_content.php';
  return ob_get_clean();
}

add_shortcode( 'img_content', 'ketchup_img_content' );
?>