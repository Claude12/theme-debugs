<?php

  $formAbove = get_field('form_above_content'); // true - content, false - form
  $content = get_field('content');
  $ninjaForm = get_field('km_ninja_form_select'); 
  $maxWidth = get_field('section_content_width');
  $sectionTitle = get_field('section_title');
  $blockInitialClasses = ['gcc-section','km-wysiwyg'];

  if($formAbove) array_push($blockInitialClasses, 'gccs-form-first');

  $blockClasses = createClasses($blockInitialClasses, 'section_content');
  $initContentClasses = ['gccs-content','km-wysiwyg','gcc-part'];
  $bulletColour = get_field('section_bullet_colour')['picker']['slug'];
  $linkColour = get_field('section_link_colour')['picker']['slug'];

  if($bulletColour) array_push($initContentClasses, 'has-' . $bulletColour . '-bullet-colour');
  if($linkColour) array_push($initContentClasses, 'has-' . $linkColour . '-link-colour');
  $contentClasses = createClasses($initContentClasses, 'section_content_colour');

  $contentProps = [];
  if( $maxWidth ) $contentProps['max-width'] = $maxWidth;

  $titleSet = ['gccs-title'];
  $titleClasses = createClasses($titleSet , 'section_title_colour');

?>

<div 
  class="<?php echo $blockClasses; ?>" 
  <?php if(!empty($contentProps)) echo populateStyleAttribute($contentProps); ?>> 


  <div class="gccs-wrap">

      <?php // Card Title ?>
      <?php if($sectionTitle) : ?>
        <h2 class="<?php echo $titleClasses; ?>"><?php echo $sectionTitle; ?></h2>
      <?php endif; ?>

      <?php // Card Content ?>
      <?php if($content) : ?>
        <div class="<?php echo $contentClasses; ?>" >
          <?php echo $content; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php // Ninja Form ?>
    <?php if($ninjaForm && function_exists('Ninja_Forms')) : ?>
      <div class="gccs-form"><?php Ninja_Forms()->display($ninjaForm); ?></div>
    <?php endif; ?>

</div>