<?php

  $data = $args['watermark_options'];
  $image = $data['watermark'] ?: null;
  if(!$image) return;

  $blockClasses = ['km-watermark', 'desktop-only'];

  $location = $data['watermark_location'];
  $animationState = $data['enable_animation'] ? 'enabled' : 'disabled';
  $offsetWatermark = $data['offset_watermark'] ? 'offset' : null;

  if($location) array_push($blockClasses, 'km-watermark-' . $location);
  if($animationState) array_push($blockClasses, 'km-animation-' . $animationState);
  if($offsetWatermark) array_push($blockClasses, 'km-watermark-' . $offsetWatermark);

?>

<div class="<?php echo implode(' ', $blockClasses) ; ?>">
  <img src="<?php echo $image; ?>" alt="section watermark" />
</div>