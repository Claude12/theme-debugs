<?php

function ketchup_recent_posts( $atts = [], $content = null, $tag = '' ) {
  ob_start();
  include get_stylesheet_directory() . '/template-parts/shortcodes/shortcode-recent-posts/shortcode-recent-posts.php';
  return ob_get_clean();
}

add_shortcode( 'recent_posts', 'ketchup_recent_posts' );
?>