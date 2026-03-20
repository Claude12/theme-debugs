<?php
/**
 * Shortcode renders single link
 * usage: [arrow_link external=true target="https://www.google.co.uk" classes="has-yellow-1-fill has-white-colour" margin="100px 0 0 0"]Link to google[/arrow_link]
 */
  $props = is_array($atts) ? $atts : [];
  $external = array_key_exists('external', $props) ? boolval($props['external']) : false;
  $url = array_key_exists('target', $props) ? $props['target'] : '#0';
  $classes = array_key_exists('classes', $props) ? explode(' ',$props['classes']) : [];
  $text = empty($content) ? 'Button Title' : $content;
  $target_attr = $external ? 'target="_blank" rel="noopener noreferrer"' : null;
  $linkClasses = array_merge(['km-single-link-wrap'], $classes);
  
  $linkProps = [];
  $margin = array_key_exists('margin', $props) ? $props['margin'] : false;

  if($margin) $linkProps['margin'] =  $margin;

?>

<div class="<?php echo implode(' ', $linkClasses); ?>" <?php echo populateStyleAttribute($linkProps); ?>>
  <a 
    class="km-single-link"
    href="<?php echo $url; ?>" 
    <?php if($target_attr) echo $target_attr; ?> >
    <span class="km-single-link-svg">
      <svg>
        <use href="#bullet-icon"/>
      </svg>
    </span>
    <span class="km-single-link-title"><?php echo $text; ?></span>
  </a>
</div>
