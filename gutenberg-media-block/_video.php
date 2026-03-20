<?php

  $video = $args['video'];
  $poster = $args['poster'];
  $controls = $args['video_options'];
  $showAudioControl = $args['show_audio_control'];
  $fitVideo = get_field('fit_video');
  $nativeControls = get_field('native_controls');

?>


<video class="km-mb-video lazy-bg-video" preload="none" 
<?php if($controls === 'autoplay') echo 'autoplays'; ?> 
<?php if($fitVideo) echo 'fitparent'; ?> 
<?php if($nativeControls) echo 'controls'; ?>
 muted loop playsinline 
poster="<?php echo $poster; ?>">
  <source data-src="<?php echo $video; ?>" type="video/mp4">  
  <p>Your browser does not support this video.</p> 
</video>

<?php // Module Overlay ?>
<?php
  get_template_part('template-parts/common/module-overlay/module-overlay', null, [
    'overlay' => get_field('media_overlay')
  ]);
?>

<?php if($controls === 'buttons' || $showAudioControl) : ?>
  <?php if(!$nativeControls) : ?>
  <div class="km-mb-video-controls">

      <?php if($controls === 'buttons') : ?>
        <button class="km-mb-control km-mb-state-control"><span class="km-icon km-icon-play">Play / Pause</span></button>
      <?php endif; ?>

      <?php if($showAudioControl) : ?>
        <button class="km-mb-control km-mb-audio-control"><span class="km-icon km-icon-sound-off">Unmute / Unmute</span></button>
      <?php endif; ?>

    </div>
  <?php endif; ?>
<?php endif; ?>
