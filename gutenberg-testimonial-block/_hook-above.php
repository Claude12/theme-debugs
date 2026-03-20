<?php 
  $navColour = get_field('navigation_colour')['picker']['slug'];
  $mainTitle = get_field('main_title') ?: null;
  $introClasses = createClasses(['km-tb-title'], 'title_colour');
  $titleClasses = createClasses([], 'title_colour');
?>

<?php if($navColour) : ?>
  <div class="km-nav-colour-prop" km-nav-colour="<?php echo $navColour; ?>">&nbsp;</div>
<?php endif; ?>

<?php // Module background Image ?>

  <?php
    get_template_part('template-parts/common/module-background-image/module-background-image', null, [
      'image' => get_field('image'),
      'image_props' => get_field('image_props'),
    ]);
  ?>

  <?php // Module Overlay ?>
  <?php
    get_template_part('template-parts/common/module-overlay/module-overlay', null, [
      'overlay' => get_field('overlay')
    ]);
  ?>

<?php // Main Title ?>
<?php if($mainTitle) : ?>
  <div class="<?php echo $introClasses; ?>">
    <h2 class="<?php echo $titleClasses; ?>"><?php echo $mainTitle; ?></h2>
  </div>
<?php endif; ?>