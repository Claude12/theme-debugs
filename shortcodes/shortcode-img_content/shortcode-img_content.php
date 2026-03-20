<?php
// [img_content img="absolute url to img" title="This is my title" classes="extra classes"]my content[/img_content]
  $props = is_array($atts) ? $atts : [];
  $classes = array_key_exists('classes', $props) ? $props['classes'] : null;
  $text = empty($content) ? 'Awaiting content' : $content;
  $title = array_key_exists('title', $props) ? $props['title'] : null;
  $img = array_key_exists('img', $props) ? $props['img'] : null;
  $img_alt = array_key_exists('img_alt', $props) ? $props['img_alt'] : ($title ? $title :'illustrative image');
  $width = array_key_exists('width', $props) ? intval($props['width']) : null;
?>


<article class="shortcode-img_content<?php if($classes) echo ' ' . trim($classes); ?>" <?php if($width) echo 'style="max-width: ' . $width . 'px;"'; ?>>

  <?php if($img) : ?>
    <img class="sic-image" src="<?php echo $img; ?>" alt="<?php echo $img_alt; ?>" />
  <?php endif; ?>

  <div class="sic-content">
    <?php if($title) : ?><h3 class="sic-title"><?php echo $title; ?></h3><?php endif; ?>
    <div class="sic-content-wrap"><?php echo do_shortcode($text); ?></div>
  </div>

</article>