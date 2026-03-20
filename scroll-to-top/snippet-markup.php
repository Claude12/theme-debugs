<?php 

  $config = get_field('scroll_to_top','option');
  $disabled = $config['disabled'] ?: false;
  $iconPicker = $config['icon']['picker'];
  $bgPicker = $config['background']['picker'];
  $colourPicker = $config['text']['picker'];

  $icon = $iconPicker['value'] ?: 'stt-icon';
  $background = $bgPicker['slug'] ?  'has-' . $bgPicker['slug'] . '-background-colour' : null;
  $colour = $colourPicker['slug'] ? 'has-' . $colourPicker['slug'] . '-fill' : null;

  $distance = $config['distance'] ?: 10;
  $speed = $config['scroll_speed'] ?: 400;
  $classes = ['stt-wrap'];

  if($background) array_push($classes, $background);
  if($colour) array_push($classes, $colour);

?>

<?php if(!$disabled) : ?>
  <button class="scroll-to-top" id="scroll-to-top" data-reveal="<?php echo $distance; ?>" data-speed="<?php echo $speed; ?>">
    <span class="<?php echo implode(' ', $classes); ?>">
      <svg class="stt-icon">
        <use xlink:href="#<?php echo $icon; ?>" />
      </svg>
    </span>
  </button>
<?php endif; ?>
