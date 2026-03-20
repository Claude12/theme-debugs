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