<?php
/*
 <?php // Module Overlay ?>
  <?php
    get_template_part('template-parts/common/module-overlay/module-overlay', null, [
      'overlay' => get_field('overlay')
    ]);
  ?>
  */


 $defaultOverlay = [
   'overlay_opacity' => '0',
   'overlay_colour' => [
     'picker' => [
       'value' => '#000'
     ]
   ]
 ];

  $overlay  = array_key_exists('overlay', $args) ? $args['overlay'] : $defaultOverlay;
  $opacity = $overlay['overlay_opacity'];
  $overlayColour = $overlay['overlay_colour']['picker']['value'] ?: $defaultOverlay['overlay_colour']['picker']['value'];
  
  if(!$opacity || $opacity === '0') return;

  $props = [];

  if($opacity) $props['opacity'] = $opacity;
  if($overlayColour) $props['background-color'] = $overlayColour;

?>

<div class="km-module-overlay" <?php echo populateStyleAttribute($props); ?>>&nbsp;</div>