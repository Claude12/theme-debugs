<?php

    $video = $args['video_url'];
    $poster = $args['poster'];
  
    if(!$video || !$poster) return;

  ?>

<?php // VIDEO ?>
<div class="video-bg-wrapper">
  <video class="video-block lazy-bg-video" preload="none" data-fit-parent autoplay muted loop playsinline poster="<?php echo $poster; ?>"> 
    <source data-src="<?php echo $video; ?>" type="video/mp4">  
    <p>Your browser does not support this video.</p> 
  </video>
</div>