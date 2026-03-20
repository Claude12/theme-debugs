<?php
  //[km_button external=true url="https://www.google.co.uk" classes="has-yellow-1-fill has-white-colour"]Link to google[/km_button]
  $props = is_array($atts) ? $atts : [];
  $external = array_key_exists('external', $props) ? boolval($props['external']) : false;
  $classes = array_key_exists('classes', $props) ? $props['classes'] : null;
  $link = array_key_exists('link', $props) ? $props['link'] : 'No link provided';
  $text = empty($content) ? '' : $content;
  $target_attr = $external ? 'target="_blank" rel="noopener noreferrer"' : null;
?>

<a class="km-inline-button cta-button cta-hover-x<?php if($classes) echo ' ' . trim($classes); ?>"  <?php if($target_attr) echo $target_attr; ?> href="<?php echo $link; ?>" > 
  <span><?php echo do_shortcode($text); ?></span>
</a>