<?php 

  $slides = $args['slides'] ?: [];

  if(empty($slides)) return;

  $controlsColour = $args['controls_colour']['picker']['slug'];
  $arrows = $args['arrows'] ?: false;
  $pagination = $args['pagination'] ?: false;
  $titleClasses = ['km-carousel-sc-title'];
  if($args['title_colour']['picker']['slug']) array_push($titleClasses, 'has-' . $args['title_colour']['picker']['slug'] . '-colour');


?>

<div class="swiper-container km-slide-carousel">
  <div class="swiper-wrapper">
    <?php foreach ($slides as &$slide) : ?>
      <?php 

        $overlayOpacity = $slide['overlay_opacity'];
        $overlayBg = $slide['overlay_bg'];
        $overlayProps = [];
        $image = $slide['image'] ?: null;
        $imageProps = [];
    
        if($image) $imageProps['background-image'] = 'url(' . $image . ')';

        if($overlayOpacity) $overlayProps['opacity'] = $overlayOpacity;

        // Title on top of slider
        $pageId = km_get_page_id();
        $pageTitle = get_the_title($pageId);
        $title = '';
        $hasCtas = is_array($slide['ctas_links']) && !empty($slide['ctas_links']);

        if($pageId && $pageTitle && $slide['use_page_title']) {
          $title = get_the_title(km_get_page_id($pageId));
        } else {
          $title = $slide['title'];
        }
      ?>
      <div class="swiper-slide<?php if($hasCtas) echo ' km-slide-has-ctas'; ?>">

        <?php // Title ?>
        <?php if($title) : ?>
          <div class="km-crt-wrap">
            <p class="<?php echo implode(' ', $titleClasses); ?>"><?php echo $title; ?></p>
          </div>
        <?php endif; ?>

        <?php // IMAGE ?>
        <?php if($slide['slide_type'] === 'image') : ?>
          <div class="km-carousel-image" <?php if(!empty($imageProps)) echo populateStyleAttribute($imageProps); ?>>&nbsp;</div>
        <?php endif; ?>

        <?php // VIDEO ?>
        <?php if($slide['slide_type'] === 'video') : ?>
          <div class="km-carousel-media-video">
            <?php get_template_part('template-parts/gutenberg-hero-carousel/slide-carousel/km-video', null, [
              'poster' => array_key_exists('poster', $slide) ? $slide['poster'] : null,
              'video_url' => array_key_exists('video_url', $slide) ? $slide['video_url'] : null,
              ]); ?>      
          </div>
        <?php endif; ?>

        <?php // Carousel overlay  ?>
        <?php if($slide['overlay_bg'] && !empty($overlayBg['picker']['slug'])) : ?>
          <div class="carousel-slide-overlay<?php if($slide['overlay_bg']) echo ' has-' . $overlayBg['picker']['slug'] . '-background-colour';  ?>" <?php echo populateStyleAttribute($overlayProps); ?>>&nbsp;</div>
        <?php endif; ?>

      </div>
    <?php endforeach; unset($slide); ?>
  </div>

  <?php if($pagination) : ?>
      <div class="swiper-pagination <?php if(!empty($controlsColour)) echo 'has-' . $controlsColour . '-colour has-' . $controlsColour . '-fill'; ?>"></div>
  <?php endif; ?>

  <?php if($arrows) : ?>
      <div class="swiper-button-prev <?php if(!empty($controlsColour)) echo 'has-' . $controlsColour . '-fill';?>">
          <svg>
              <use xlink:href="#carousel-left" />
          </svg>
      </div>
      <div class="swiper-button-next <?php if(!empty($controlsColour)) echo 'has-' . $controlsColour . '-fill';?>">
          <svg>
              <use xlink:href="#carousel-right" />
          </svg>
      </div>
  <?php endif; ?>

</div>

