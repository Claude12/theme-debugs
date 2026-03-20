<?php

  $carouselData = '';
  $carouselSettings = [
    'autoplay' => get_field('autoplay'),
    'speed' => get_field('speed'),
    'loop' => get_field('loop'),
    'pagination' => get_field('pagination'),
    'arrows' => get_field('side_arrows')
 ];

  foreach ($carouselSettings as $key => $prop) {
    if(empty($prop)) continue;
    $carouselData .= 'data-' . $key . '="' . $prop . '" ';
  }

  $images = $args['images'] ?: [];
  if(empty($images)) return;
  
?>

<div class="km-mb-gallery" <?php echo $carouselData; ?>>

  <div class="swiper-container">
    <div class="swiper-wrapper">
      <?php foreach($images as $image) : ?>
        <?php 
          $bgProps = [];
          $url = $image['url'];
          $alt = $image['alt'] ?: $image['name'];

          if($url) $bgProps['background-image'] = 'url(' . $url . ')';

        ?>
        <div class="swiper-slide">
          <span class="km-mb-image" <?php echo populateStyleAttribute($bgProps); ?>><?php echo $alt; ?></span>
          <?php // Module Overlay ?>
          <?php
            get_template_part('template-parts/common/module-overlay/module-overlay', null, [
              'overlay' => get_field('media_overlay')
            ]);
          ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if(get_field('pagination')) : ?>
      <div class="swiper-pagination"></div>
  <?php endif; ?>

  <?php if(get_field('side_arrows')) : ?>
      <button class="km-mb-arrow km-mb-prev">
        <span class="km-icon km-icon-prev">Prev</span>
      </button>
      <button class="km-mb-arrow km-mb-next">
        <span class="km-icon km-icon-next">Next</span>
      </button>
  <?php endif; ?>

</div>
