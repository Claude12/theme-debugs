<?php 
  $setup = $template_args['setup'];
  $slides = $setup['data'] ?: [];
  $sectionId = array_key_exists('section-id', $setup) ?  $setup['section-id'] : null;
  $sectionTitle = array_key_exists('section-title', $setup) ? $setup['section-title'] : 'block-' . uniqid();
  $extraClasses = array_key_exists('section-extra-classes', $setup) ? implode(' ', $setup['section-extra-classes']) : null;
  $sectionClasses = 'km-' . $sectionTitle;
  $wrapperSource = explode('-', $sectionTitle);
  $wrapperClass = [];

  $hookAboveArgs = array_key_exists('hook-above-args', $setup) ? $setup['hook-above-args'] : [];
  $hookBelowArgs = array_key_exists('hook-below-args', $setup) ? $setup['hook-below-args'] : [];

  if($extraClasses) $sectionClasses .= ' ' . $extraClasses;
  foreach($wrapperSource as $word) array_push($wrapperClass, substr($word, 0, 1));

  $swiper = array_values($template_args['swiper-config'])[0];

  $nav = $swiper['navigation'] === true ? 'true' : 'false';
  $navType = $swiper['navigation_type'] === true ? 'bullets' : 'fraction';

  $swiperConfig = [
    'autoplay' => $swiper['autoplay'] == false ? 'false' : 'true',
    'slide-interval' => $swiper['slide_interval'] ? intVal($swiper['slide_interval']) : 3000,
    'speed' => $swiper['speed'] ?: 600,
    'auto-height' => $swiper['auto_height'] == false ? 'false' : 'true',
    'loop' => $swiper['loop'] == false ? 'false' : 'true',
    'nav' => $navType,
  ];
  
  // Disable auto height within admin area
  if(is_admin()) $swiperConfig['auto-height'] = 'false';

  if($swiper['slides_per_view']) $swiperConfig['slides-per-view'] = $swiper['slides_per_view'];

  $swiperData = null;
  foreach($swiperConfig as $option => $val) {
    $swiperData .= 'data-' .$option . '="' . $val .'" ';
  }

  // Navigation
  $nav = $swiper['navigation'];
  $sideArrows = $swiper['side_navigation_arrows'];
?>

<section class="<?php echo $sectionClasses; ?>" <?php if($sectionId) echo 'id="'. $sectionId . '"'; ?><?php if($swiperData) echo $swiperData; ?>>
  <?php if(array_key_exists('hook-above',$setup)) get_template_part($setup['hook-above'], null, $hookAboveArgs); ?>

  <div class="km-<?php echo implode('', $wrapperClass); ?>-wrap">

  <?php if($sideArrows) include(plugin_dir_path( dirname( __FILE__ ) ) . 'swiper/_prev-btn.php');  ?>

    <div class="swiper-container">
      <div class="swiper-wrapper">
        <?php foreach($slides as $slide) : ?>

        <?php
            $slideArgs = [
              'item' => $slide
            ];

            if(array_key_exists('config',$template_args)) {
              $slideArgs['config'] = $template_args['config'];
            }
          ?>
          <div class="swiper-slide">
            <?php get_template_part($setup['slide-markup-path'], null, $slideArgs); ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if($sideArrows) include(plugin_dir_path( dirname( __FILE__ ) ) . 'swiper/_next-btn.php');  ?>

  </div> 

  <?php if($nav) : ?>
    <div class="swiper-info-wrap">
      <?php  include(plugin_dir_path( dirname( __FILE__ ) ) . 'swiper/_prev-btn.php'); ?>
      <div class="swiper-info fn-<?php echo $navType; ?>"></div>
      <?php include(plugin_dir_path( dirname( __FILE__ ) ) . 'swiper/_next-btn.php'); ?>
    </div>

  <?php endif; ?>

  <?php if(array_key_exists('hook-below',$setup)) get_template_part($setup['hook-below'], null , $hookBelowArgs); ?>
  
</section>

