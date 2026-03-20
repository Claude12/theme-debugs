<?php 

  $slide = $args['slide'];
  $count = $args['index'];
  $view = $args['isMobile'];
  $mediaType = $slide['media_type'];

  $videoUrl = $slide['video'][$view];
  $poster = $slide['poster'][$view];
  $bgProps = $slide['image_properties'][$view];

  $imageStyles = [
    'background-image' =>  'url(' . $slide['image'][$view] . ')',
    'background-position' => $bgProps['bg_position'],
    'background-repeat' => $bgProps['bg_repeat'],
    'background-size' => $bgProps['bg_size']
  ];

?>

<?php // Image ?>
<?php if($mediaType === 'image')  : ?>
  <div class="km-carousel-image<?php if(!is_admin()) echo ' lazy-bg-image'; ?>" <?php echo populateStyleAttribute($imageStyles); ?>>&nbsp;</div>
<?php endif; ?>

<?php // Video ?>
<?php if($mediaType === 'video')  : ?>
  <div class="video-bg-wrapper">
  <video class="video-block lazy-bg-video" preload="none" data-fit-parent autoplay muted loop playsinline poster="<?php echo $poster; ?>"> 
    <source data-src="<?php echo $videoUrl; ?>" type="video/mp4">  
    <p>Your browser does not support this video.</p> 
  </video>
</div>
<?php endif; ?>
