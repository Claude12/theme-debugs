<?php
 
 /*

  <?php // Module background Image ?>
  <?php

    get_template_part('template-parts/common/module-background-image/module-background-image', null, [
      'image' => get_field('image'),
      'image_props' => get_field('image_props'),
    ]);

  ?>

 */

  $defaultImageProps = [
    'bg_position' => null,
    'bg_repeat' => null,
    'bg_size' => null
  ];

  $img  = array_key_exists('image', $args) ? $args['image'] : null;
  $imgProps = array_key_exists('image_props', $args) ? $args['image_props'] : $defaultImageProps;
  $bgPos = $imgProps['bg_position'];
  $bgRepeat = $imgProps['bg_repeat'];
  $bgSize = $imgProps['bg_size'];

  if(!$img) return;

  $props = [
    'background-image' => 'url(' . $img . ')'
  ];

  if($bgPos) $props['background-position'] = $bgPos;
  if($bgRepeat) $props['background-repeat'] = $bgRepeat;
  if($bgSize) $props['background-size'] = $bgSize;
?>


<div class="km-module-background-image" <?php echo populateStyleAttribute($props); ?>>&nbsp;</div>