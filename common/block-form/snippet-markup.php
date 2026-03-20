<?php

  $data = $args['form'];


  // Content
  $form = $data['km_ninja_form_select'] ?: null;
  $content = $data['content'] ?: null;
  $buttons = $data['form_buttons'] ?: [];

  // Colours
  $bgColour = $data['bg_colour']['picker']['slug'] ?: null;
  $contentColour = $data['content_colour']['picker']['slug'] ?: null;
  $linkColour = $data['link_colour']['picker']['slug'] ?: null;
  $bulletColour = $data['bullet_colour']['picker']['slug'] ?: null;
  $contentClasses = ['km-common-form-content','km-wysiwyg'];
  $blockClasses = ['km-common-form'];
  $linkColour = $data['link_colour']['picker']['slug'];
  $bulletColour = $data['bullet_colour']['picker']['slug'];


  if($bgColour) array_push($blockClasses, 'has-' . $bgColour . '-background-colour');
  if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour' );
  if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');
  if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour . '-bullet-colour');

  if(!$form && !$content && empty($buttons['links'])) return;


 ?>

 <article class="<?php echo implode(' ', $blockClasses); ?>">
  
  <?php // CONTENT ?>
  <div class="<?php echo implode(' ', $contentClasses); ?>"><?php echo $content; ?></div>

  <?php // FORM ?>
  <?php if($form && function_exists('Ninja_Forms')) : ?>
    <div class="km-common-form-element"><?php Ninja_Forms()->display($form); ?></div>
  <?php endif; ?>

  <?php // CTAS ?>
  <?php if(!empty($buttons['links'])) : ?>
    <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
      'links' => $buttons['links'],
      'classPrefix' => 'km-common-form-btns', // optional
      'extraButtonClass' => 'cta-hover-y' // optional
    ] );
    ?>   
  <?php endif; ?>
 

 </article>