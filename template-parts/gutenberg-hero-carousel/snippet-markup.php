
<?php

  $defaultBanners = get_field('default_hero_banners', 'option') ?: [];
  $defaultBanner = null;

  foreach ($defaultBanners as &$banner) {
    $postType = $banner['km_default_banners_post_type'];

    if(is_404() && $postType === '404') {
      $defaultBanner = $banner;
      break;
    }

    if(is_search() && $postType === 'search') {
      $defaultBanner = $banner;
      break;
    }

    if($postType === get_post_type() && !is_404()){
      $defaultBanner = $banner;
      break;
    }

  }
  unset($banner);

  // No default banner established. Must be generic
  if(!$defaultBanner) {
    foreach ($defaultBanners as &$banner) {
      if($banner['km_default_banners_post_type'] === 'generic') $defaultBanner = $banner;
    }
  }

  $id = km_get_page_id();
 
  if($id) {
    $pageObject = get_post( $id );
    $blocks = parse_blocks( $pageObject->post_content );
    $pageHasHero = has_block( 'acf/hero-carousel' , $pageObject);
  } else {
    $pageHasHero = false;
  }

  $heroArgs = [
    'main' => [
      'acf-classes' => array_key_exists('acf-classes',$args) ? $args['acf-classes'] : [],
      'height' => $pageHasHero ? get_field('height') : $defaultBanner['height'],
      'animate_content_entry' => $pageHasHero ? get_field('animate_content_entry') : $defaultBanner['animate_content_entry'],
      'autoplay' => $pageHasHero ? get_field('autoplay') : $defaultBanner['autoplay'],
      'speed' => $pageHasHero ? get_field('speed') : $defaultBanner['speed'],
      'loop'=> $pageHasHero ? get_field('loop') : $defaultBanner['loop'],
      'pagination'=> $pageHasHero ? get_field('pagination') : $defaultBanner['pagination'],
      'arrows' => $pageHasHero ? get_field('arrows') : $defaultBanner['arrows'],
      'content_location' => $pageHasHero ? get_field('content_location') : $defaultBanner['content_location'],
    ],
    'slide-carousel' => [
      'slides' => $pageHasHero ? get_field('slides') : $defaultBanner['slides'],
      'title_colour' => $pageHasHero ? get_field('title_colour') : $defaultBanner['title_colour'],
      'controls_colour' =>  $pageHasHero ? get_field('controls_colour') : $defaultBanner['controls_colour'],
      'pagination'=> $pageHasHero ? get_field('pagination') : $defaultBanner['pagination'],
      'arrows' => $pageHasHero ? get_field('arrows') : $defaultBanner['arrows']
    ],
    'content-carousel' => [
      'slides' => $pageHasHero ? get_field('slides') : $defaultBanner['slides'],
      'content_background' => $pageHasHero ? get_field('content_background') : $defaultBanner['content_background'],
      'title_colour' => $pageHasHero ? get_field('title_colour') : $defaultBanner['title_colour'],
      'content_colour' => $pageHasHero ? get_field('content_colour') : $defaultBanner['content_colour'],
      'bullets_colour' => $pageHasHero ? get_field('bullets_colour') : $defaultBanner['bullets_colour'],
      'link_colour' => $pageHasHero ? get_field('link_colour') : $defaultBanner['link_colour']
    ],
    'scroll-down' => [
      'enabled' => $pageHasHero ? get_field('scroll_down_enabled') : $defaultBanner['scroll_down_enabled'],
      'title' => $pageHasHero ? get_field('scroll_down_title') : $defaultBanner['scroll_down_title'],
      'icon' => $pageHasHero ? get_field('scroll_down_icon') : $defaultBanner['scroll_down_icon'],
      'controls_colour' =>  $pageHasHero ? get_field('controls_colour') : $defaultBanner['controls_colour'],
    ]
  ]; 

  $data = $heroArgs['main'];
  $height = $data['height'] ?: '70';
  $autoPlay = $data['autoplay'];
  $speed = $data['speed'];
  $loop = $data['loop'];
  $carouselProps = [];
  $pagination = $data['pagination'];
  $arrows = $data['arrows'];
  $animate = $data['animate_content_entry'] ? 'enabled' : 'disabled';

  if($autoPlay) $carouselProps['autoplay'] = $autoPlay;
  if($speed) $carouselProps['speed'] = $speed;
  if($loop) $carouselProps['loop'] = $loop;
  if($pagination) $carouselProps['pagination'] = $pagination;
  if($arrows) $carouselProps['arrows'] = $arrows;

  $settings = '';

  foreach ($carouselProps as $key => &$prop) {
    $settings .= 'data-' . $key . '="' . $prop .'" ';
  }
  unset($prop);
  $heroProps = [];
  if($height) $heroProps['height'] = $height . 'vh';

  if(empty($heroArgs['slide-carousel']['slides'])) return;
?>

<div class="km-hero-carousel km-carousel-animation-<?php echo $animate; ?> km-hero-content-<?php echo $data['content_location']; if(!empty($data['acf-classes'])) echo ' ' . implode(' ', $data['acf-classes']); ?>"  
  <?php echo populateStyleAttribute($heroProps); ?>
  <?php echo $settings; ?>
 >

  <?php // Slide carousel ?>
  <?php get_template_part('template-parts/gutenberg-hero-carousel/slide-carousel/markup', null, $heroArgs['slide-carousel']); ?>

  <?php // Content carousel ?>
  <?php get_template_part('template-parts/gutenberg-hero-carousel/content-carousel/markup', null, $heroArgs['content-carousel']); ?>

  <?php // Scroll down ?>
  <?php get_template_part('template-parts/gutenberg-hero-carousel/scroll-down', null, $heroArgs['scroll-down']); ?>

</div>

