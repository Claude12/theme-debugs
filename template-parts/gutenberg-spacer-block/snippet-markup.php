 
<?php  

  $addCustom = get_field('add_custom_colour');
  $bgColour = $addCustom ? get_field('selected_bg') : get_field('picked_bg')['picker']['slug'];
  $inlineProps = [];

  // Background Colour
  if($addCustom && $bgColour) $inlineProps['background-color'] = $bgColour;

  // Image props if image is used
  $imageData = get_field('image') ? : [];
  $imgUrl = array_key_exists('url', $imageData) ? $imageData['url'] : null;
  $bg = get_field('image_configuration') ?: [];

  $rVertical = array_key_exists('vertical', $bg) ? $bg['vertical'] : 'top';
  $rHorizontal = array_key_exists('horizontal', $bg) ? $bg['horizontal'] : 'left';

  $bgPos =  implode(' ',[ $rHorizontal , $rVertical]);
  $bgRepeat = array_key_exists('background_repeat', $bg) ? $bg['background_repeat'] : [];
  $repeatProp = 'no-repeat';

  switch (sizeof($bgRepeat)) {
    case 1:
      $repeatProp = 'repeat-' . $bg['background_repeat'][0];
      break;
    case 2:
      $repeatProp = 'repeat';
      break;
    default:
      $repeatProp = 'no-repeat';
  }

  if($imgUrl) {
    $inlineProps['background-image'] = "url('" . $imgUrl . "')";
    $inlineProps['background-repeat'] = $repeatProp;
    $inlineProps['background-position'] = $rVertical . ' ' . $rHorizontal;
  }


  $blockClasses = array_merge(['km-spacer-block'],$args['acf-classes']);

  if(!$addCustom && $bgColour) array_push($blockClasses, 'has-' . $bgColour . '-background-colour');

  // Dimensions
  $lHeight = get_field('height_large') ?: '200';
  $lUnit = get_field('height_unit_large_screen') ?: 'px';
  $sHeight = get_field('height_small') ?: '100';
  $sUnit = get_field('height_unit_small_screen') ?: 'px';
  $inlineProps['height'] = $lHeight . $lUnit;
  
?>

<div class="<?php echo implode(' ', $blockClasses);  ?>" <?php echo populateStyleAttribute($inlineProps); ?>>
  <span class="km-spacer-inner" <?php echo populateStyleAttribute(['height' => $sHeight . $sUnit]); ?>>&nbsp;</span>
</div>