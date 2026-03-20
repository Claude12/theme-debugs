<?php
  $props = is_array($atts) ? $atts : [];
  $classes = array_key_exists('classes', $props) ? $props['classes'] : null;
  $modal = array_key_exists('modal', $props) ? $props['modal'] : 'No modal provided';
  $text = empty($content) ? '' : $content;
?>

<button class="reset-button km-cta-popup-trigger<?php if($classes) echo ' ' . trim($classes); ?>" data-modal="<?php echo $modal; ?>"> 
  <span><?php echo do_shortcode($text); ?></span>
</button>

<?php // [km-modal classes="cta-5 cta-button cta-hover-x" modal="km-modal-all-number-4-at-10"] My popup [/km-modal] ?>