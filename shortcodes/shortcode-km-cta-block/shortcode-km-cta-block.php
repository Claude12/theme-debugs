<?php
  $props = is_array($atts) ? $atts : [];
  $classes = array_key_exists('classes', $props) ? $props['classes'] : null;
  $modal = array_key_exists('modal', $props) ? $props['modal'] : false;
  $text = array_key_exists('label', $props) ? $props['label'] : 'Get in touch with us';
  $bg = array_key_exists('background', $props) ? $props['background'] : null;
  $color = array_key_exists('colour', $props) ? $props['colour'] : null;
  $styles = [];

  if($bg) $styles['background-color'] = $bg;
  if($color) $styles['color'] = $color;
?>


<div class="shortcode-cta-block" <?php echo populateStyleAttribute($styles); ?>>

  <?php if($content) : ?>
    <div class="scb-content"><p><?php echo $content; ?></p></div>
  <?php endif; ?>

  <?php if($modal) : ?>
    <button class="reset-button km-cta-popup-trigger<?php if($classes) echo ' ' . trim($classes); ?>" data-modal="<?php echo $modal; ?>"> 
      <span><?php echo do_shortcode($text); ?></span>
    </button>
  <?php endif; ?>

</div>




<?php //[km-cta-block classes="cta-5 cta-button cta-hover-x" modal="km-modal-all-number-4-at-10" label="My label"] Content[/km-cta-block] ?>