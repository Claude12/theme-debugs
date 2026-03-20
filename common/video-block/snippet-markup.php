<?php
  $video = $args['video'];
  $poster = $args['poster'];
  $muteControls = $args['mute_unmute'];
  $playPauseControls = $args['play_pause'];
  $autoplay = $args['autoplay'];
  $controlsLocation = $args['controls_location'];
  $controlsColour = $args['controls_colour'];
  $videoProps = [];

  if($muteControls) $videoProps['mute'] = $muteControls;
  if($playPauseControls) $videoProps['play-pause'] = $playPauseControls;
  if($autoplay) $videoProps['autoplay'] = $autoplay;

  $propList = '';

  foreach ($videoProps as $key => $prop) {
    $propList .= 'data-' . $key . '="' . $prop . '" ';
  }
  unset($prop);

  $blockClasses = ['km-video-block'];
  if($controlsLocation) array_push($blockClasses, $controlsLocation);

?>

<div class="<?php echo implode(' ', $blockClasses); ?>" <?php if(!empty($propList)) echo $propList; ?> >

  <?php // VIDEO ?>
  <video class="lazy-bg-video" src="<?php echo $video; ?>" allow="autoplay" loop muted playsinline poster="<?php echo $poster; ?>"> 
    <source type="video/mp4">  
    <p>Your browser does not support this video.</p> 
  </video>

  <?php // VIDEO CONTROLS ?>
  <?php if($playPauseControls || $muteControls) : ?>
      <div class="km-video-controls" >

      <?php if($playPauseControls) : ?>
        <button class="km-video-play button-reset<?php if(!empty($controlsColour)) echo ' has-' . $controlsColour . '-fill';?>">
          <svg>
            <use href="#km-video-play"></use>
          </svg>
        </button>
        <button class="km-video-pause button-reset<?php if(!empty($controlsColour)) echo ' has-' . $controlsColour . '-fill';?>">
          <svg>
            <use href="#km-video-pause"></use>
          </svg>
        </button>
        <?php endif; ?>

        <?php if($muteControls) : ?>
        <button class="km-video-mute button-reset<?php if(!empty($controlsColour)) echo ' has-' . $controlsColour . '-fill';?>">
          <svg class="km-speaker-on">
            <use href="#km-speaker-on"></use>
          </svg>
          <svg class="km-speaker-off">
            <use href="#km-speaker-off"></use>
          </svg>
        </button>
      <?php endif; ?>

    </div>
  <?php endif; ?>

</div>
