<?php 

  $slides = get_field('slides') ?: [];
  $view = wp_is_mobile() ? 'mobile' : 'desktop';
  $height = get_field('height')[$view] ?: '300';
  $heightUnit = get_field('height_unit')[$view] ?: 'vh';
  $scrollDown = get_field('scroll_down') ?: 'disabled';
  $uniqueId = 'km-carousel-' . uniqid();
  $animationEffect = get_field('animation_effect') === 'on' ?: false;
  $blockStyles = [
    'height' => $height . $heightUnit
  ];

  if(empty($slides)) return;
  
  // Carousel settings
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

?>

<div class="km-carousel<?php if($animationEffect) echo ' carousel-animation-effect'; if(!empty($args['acf-classes'])) echo ' ' . implode(' ',$args['acf-classes']); ?>" <?php echo populateStyleAttribute($blockStyles); ?> <?php echo $carouselData; ?>>

 <?php if($animationEffect) echo '<div class="km-carousel-wrap">'; ?>

  <div class="swiper-container">
    <div class="swiper-wrapper">
      <?php foreach($slides as $index => $slide) : ?>
        <div class="swiper-slide km-slide-<?php echo $index + 1; ?>">
          <?php

            get_template_part('/template-parts/gutenberg-carousel/_media', null, [
              'slide' => $slide,
              'index' => $index,
              'isMobile' => $view
            ]);

            get_template_part('template-parts/common/module-overlay/module-overlay', null, [
              'overlay' => get_field('overlay')
            ]);

            get_template_part('/template-parts/gutenberg-carousel/_content', null, [
              'slide' => $slide,
              'index' => $index,
              'isMobile' => $view,
              'colours' => [
                'background' => get_field('content_background')['picker']['slug'],
                'colour' => get_field('content_colour')['picker']['slug'],
                'links' => get_field('link_colour')['picker']['slug'],
                'bullets' => get_field('bullet_colour')['picker']['slug']
              ]
            ]);

          ?>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if($animationEffect) echo '</div>'; ?>

  </div>

  <?php if(get_field('pagination')) : ?>
      <div class="swiper-pagination"></div>
  <?php endif; ?>

  <?php if(get_field('side_arrows')) : ?>
      <button class="swiper-button swiper-button-prev">Previous</button>
      <button class="swiper-button swiper-button-next">Next</button>
  <?php endif; ?>

  <?php if($scrollDown === 'enabled') : ?>
    <a class="km-carousel-scroll" href="#<?php echo $uniqueId?>">Explore More</a>
  <?php endif; ?>

</div>

<?php if($scrollDown === 'enabled') : ?>
  <div class="km-carousel-end" id="<?php echo $uniqueId?>">&nbsp;</div>
<?php endif; ?>

