<?php
  $props = is_array($atts) ? $atts : [];
  $classes = array_key_exists('classes', $props) ? $props['classes'] : null;
  $width = array_key_exists('width', $props) ? intval($props['width']) : null;
  $text = empty($content) ? '' : $content;
  $screen_reader = array_key_exists('screen_reader', $props) ? $props['screen-reader'] : null;
?>


<article class="shortcode-block<?php if($classes) echo ' ' . trim($classes); ?>">
  <?php if($screen_reader) : ?><h2 class="sr-only"><?php echo $screen_reader; ?></h2> <?php endif; ?>
  <div class="sb-wrapper" <?php if($width) echo 'style="max-width: ' . $width . 'px;"'; ?>>
    <?php echo do_shortcode($text); ?>
  </div>
</article>