<?php 
  // Within loop
  //<? php // ICON ? >
  //<? php if($panel['icon']) get_template_part('template-parts/common/icons/icon',false, ['icon' => $panel['icon']]); ? > 

  //Get field
  //< ?php // ICON ? >
  //< ?php if(get_field('icon')) get_template_part('template-parts/common/icons/icon',false, ['icon' => get_field('icon')]); ? > 

  // With classes
/*
  <?php
    $iconArgs = [
      'icon' => $panel['icon']
    ];

    if($iconColour) $iconArgs['classes'] = ['has-' . $iconColour . '-background-colour'];
  ?>
  <?php get_template_part('template-parts/common/icons/icon',false, $iconArgs); ?> 
 */
 
   // no $args  = icon picker

  $payload = [];
  $data = array_key_exists('icon', $args) ? $args['icon'] : null;
  $config = [];
  $classes = array_key_exists('classes', $args) ? $args['classes'] : [];

  if(!isset($args)){
    $payload = $template_args['payload'];
    $data = $payload['item'];
    $config = $payload['config'];
  }

 if(!$data['slug']) return; 

?>

<?php if(!isset($args)) : ?>
  <div class="km-icon-demo">
    <span class="km-icon km-icon-<?php echo $data['slug']; ?><?php if(!empty($classes)) echo ' ' . implode(' ', $classes); ?>"></span>
    <span class="km-icon-label"><?php echo $data['label']; ?></span>
  </div> 
  <?php else : ?>
    <span class="km-icon km-icon-<?php echo $data['slug']; ?><?php if(!empty($classes)) echo ' ' . implode(' ', $classes); ?>"></span>
<?php endif; ?>