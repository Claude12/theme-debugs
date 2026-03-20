<?php

   $title = get_field('title');
   $content = get_field('content');
   $intro = get_field('intro');
   $buttons = get_field('button_links') ?: [];
   $image = get_field('image');
   $overlayOpacity = get_field('overlay_opacity') ?: 0;
   $contactForm = get_field('km_ninja_form_select');
   $contentLocation = get_field('content_location');

    // Colours
    $introColour = get_field('intro_colour')['picker']['slug'];
    $titleColour = get_field('title_colour')['picker']['slug'];
    $contentColour = get_field('content_colour')['picker']['slug'];
    $linkColour = get_field('link_colour')['picker']['slug'];
    $bulletColour = get_field('bullet_colour')['picker']['slug'];
    $backgroundColour = get_field('background_colour')['picker']['slug'];
    $overlayColour = get_field('overlay_colour')['picker']['value'];

    $blockClasses = array_merge(['km-get-in-touch'],$args['acf-classes']);
    $introClasses = ['km-git-intro'];
    $titleClasses = ['km-git-title'];
    $contentClasses = ['km-git-content','km-wysiwyg'];


    if($contentLocation) array_push($blockClasses, 'km-git-content-' . $contentLocation);
    if($introColour) array_push($introClasses, 'has-' . $introColour . '-colour');
    if($titleColour) array_push($titleClasses, 'has-' . $titleColour . '-colour');
    if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');
    if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');
    if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour . '-bullet-colour');
    if($backgroundColour) array_push($blockClasses, 'has-' . $backgroundColour . '-background-colour');


    $imageOptions = get_field('bg_options');
    $imageProps = [];
  
    if($image) {
      $imageProps['background-image'] = 'url(' . $image . ')';
      $imageProps['background-position'] = $imageOptions['bg_position'];
      $imageProps['background-repeat'] = $imageOptions['bg_repeat'];
      $imageProps['background-size'] = $imageOptions['bg_size'];
    }

    $overlayProps = [];
    if($overlayOpacity) $overlayProps['opacity'] = $overlayOpacity;
    if($overlayColour)  $overlayProps['background-color'] = $overlayColour;
    $blocks = parse_blocks( get_the_content() );
  
 ?>

<section class="<?php echo implode(' ', $blockClasses); ?>">
  <div class="km-git-wrap">

    <?php // IMAGE ?>
    <?php if($image) : ?>
      <div class="km-git-image-wrap">
        <div class="km-git-image" <?php echo populateStyleAttribute($imageProps); ?>>&nbsp;</div>
      </div>
      <div class="km-git-overlay" <?php echo populateStyleAttribute($overlayProps); ?>>&nbsp;</div>
    <?php endif; ?>
    
    <div class="km-git-item">

      <?php // INTRO ?>
      <?php if($intro) : ?>
        <span class="<?php echo implode(' ', $introClasses); ?>"><?php echo $intro; ?></span>
      <?php endif; ?>

      <?php // TITLE ?>
      <?php if($title) : ?>
        <h2 class="<?php echo implode(' ', $titleClasses); ?>"><?php echo $title; ?></h2>
      <?php endif; ?>

      <?php // CONTENT ?>
      <?php if($content) : ?>
        <div class="<?php echo implode(' ', $contentClasses); ?>"><?php echo $content; ?></div>
      <?php endif; ?>

      <?php // Buttons ?>
      <?php if(!empty($buttons)) : ?>
        <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
          'links' => $buttons,
          'classPrefix' => 'km-git', // optional
          'extraButtonClass' => 'cta-hover-x' // optional
        ] );
        ?>   
      <?php endif; ?>
    </div>

    <?php // Contact Form ?>
    <?php if($contactForm && function_exists('Ninja_Forms')) : ?>
      <div class="km-git-form"><?php Ninja_Forms()->display($contactForm); ?></div>
    <?php endif; ?>

  </div>
</section>
