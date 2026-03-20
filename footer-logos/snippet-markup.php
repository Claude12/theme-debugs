<?php

$items = get_field('footer_logos','option') ?: [];

// No items - no block
if(count($items) === 0) return;

?>

<article class="footer-logos">
  <?php foreach ($items as $item) : ?>
    <?php
      $imageData = array_key_exists('img', $item) ? $item['img'] : [];
      $linkData = array_key_exists('link', $item) ? $item['link'] : [];
      $width = array_key_exists('width', $item) ? empty($item['width']) ? null : $item['width'] . 'px' : null;

      // Link Config
      $linkTitle = is_array($linkData) ? $linkData['title'] : 'No title provided';
      $linkUrl = is_array($linkData) ? $linkData['url'] : null;
      $external = is_array($linkData) ? $linkData['target'] : false;

      // Image config
      $imgUrl = array_key_exists('url', $imageData) ? $imageData['url'] : false;
      $alt = array_key_exists('alt', $imageData) ? $imageData['alt'] ?: $imageData['title'] : 'Image';
    ?>

    <div class="fl-item">

     <?php // Logo ?>
     <?php if($imgUrl) : ?>
      <img class="fli-img" src="<?php echo $imgUrl; ?>" alt="<?php echo $alt; ?>" <?php if($width) echo populateStyleAttribute(['max-width' => $width]); ?> />
     <?php endif; ?>

     <?php // Link ?>
      <?php if($linkUrl) : ?>
        <a class="fli-link" href="<?php echo $linkUrl; ?>" <?php if($external) echo 'target="_blank" rel="noopener"'; ?>><?php echo $linkTitle; ?></a>
      <?php endif; ?>

    </div>

  <?php endforeach; ?>
</article>