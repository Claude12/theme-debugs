<?php
/**
 * Shortcode renders single link
 * usage: [block_link external=true url="https://www.google.co.uk" classes="has-yellow-1-fill has-white-colour" margin="100px 0 0 0"]Link to google[/block_link]
 * add km-block-link-active to make it in hover state from the beginning
 */
  $props = is_array($atts) ? $atts : [];
  $external = array_key_exists('external', $props) ? boolval($props['external']) : false;
  $url = array_key_exists('url', $props) ? $props['url'] : '#0';
  $classes = array_key_exists('classes', $props) ? explode(' ',$props['classes']) : [];
  $text = empty($content) ? 'Button Title' : $content;
  $target_attr = $external ? 'target="_blank" rel="noopener noreferrer"' : null;
  $linkClasses = array_merge(['km-block-link'], $classes);
  

  $linkProps = [];
  $margin = array_key_exists('margin', $props) ? $props['margin'] : false;

  if($margin) $linkProps['margin'] =  $margin;

?>

<div class="km-block-link-wrap" <?php echo populateStyleAttribute($linkProps); ?>>
  <a 
    class="<?php echo implode(' ', $linkClasses); ?>"
    href="<?php echo $url; ?>" 
    <?php if($target_attr) echo $target_attr; ?> >
    <span class="km-block-link-title"><?php echo $text; ?></span>
  </a>
</div>
