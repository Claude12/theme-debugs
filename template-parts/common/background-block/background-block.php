<?php 
  $background = ($template_args['background']);
  $img = $background['img'];
  $height = !empty($background['size']['height']) ? $background['size']['height'] : null;
  $opacity = !empty($background['opacity']) ? $background['opacity'] : '1';
  $repeat = $background['repeat_directions'];
  $bg_type = $background['background_type'];
  $bg = 'style="background-color:' . $background['background_bg'] . ';"';


  $bgPixels = array(
    'width' => !empty($background['pixel_size']['width']) ? $background['pixel_size']['width'] . 'px' : 'auto',
    'height' => !empty($background['pixel_size']['height']) ? $background['pixel_size']['height'] . 'px' : 'auto'
  );

  $pixels = (implode('', $bgPixels) === 'autoauto') ? 'auto' : implode(' ',$bgPixels);
  $bgSize = $bg_type === 'pixels' ? $pixels : $bg_type;

  $repeatProp = 'no-repeat';

    switch (sizeof($repeat)) {
      case 1:
        $repeatProp = 'repeat-' . $repeat[0];
        break;
      case 2:
        $repeatProp = 'repeat';
        break;
      default:
        $repeatProp = 'no-repeat';
    }

  $backgroundProps = array(
    'background-image:url(' . $img['url'] .');',
    'background-repeat:' . $repeatProp . ';',
    'background-size:' . $bgSize . ';',
    'background-position:' . implode(' ', $background['location']) . ';'
  );

  if($background["opacity"] !== '1') array_push($backgroundProps, 'opacity:' . $background["opacity"] . ';');

  // Overlay
  $overlayProps = [];
  $overlayColour = $background['overlay_colour'];
  $overlayOpacity = $background['overlay_opacity'];

  $overlayProps['background-color'] = $overlayColour;
  $overlayProps['opacity'] = $overlayOpacity;

?>


<?php //if($img) : ?>

  <div class="background-container" <?php if(!empty($background['background_bg'])) echo $bg; ?>>
    <?php if($img['url']) : ?>
      <div class="background" style="<?php echo implode('', $backgroundProps); ?>">&nbsp;</div>
    <?php endif; ?>
    <?php if(!empty($overlayProps) && $img) : ?>
      <div class="background-container-overlay"<?php echo populateStyleAttribute($overlayProps); ?>>&nbsp;</div>
    <?php endif; ?>
  </div>

<?php //endif; ?>