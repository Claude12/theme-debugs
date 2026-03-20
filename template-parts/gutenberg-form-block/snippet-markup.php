
    <?php
    $title = get_field('title');
    $content = get_field('content');
    $ctas = get_field('buttons') ? get_field('buttons')['links'] ?: [] : [];
  
    // Classes
    $extraClasses = get_field('extra_classes') ?: '';
    $blockClasses = array_merge(['km-form-block'],$args['acf-classes']);
    $titleClasses = ['km-fb-title'];
    $contentClasses = ['km-fb-content', 'km-wysiwyg'];
  
    // Colours
    $backgroundColour = get_field('bg_colour')['picker']['slug'];
    $titleColour = get_field('title_colour')['picker']['slug'];
    $contentColour = get_field('content_colour')['picker']['slug'];
    $linkColour = get_field('link_colour')['picker']['slug'];
    $bulletColour = get_field('bullet_colour')['picker']['slug'];
  
    if($backgroundColour) array_push($blockClasses, 'has-' . $backgroundColour . '-background-colour');
    if($titleColour) array_push($titleClasses, 'has-' . $titleColour . '-colour');
    if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');
    if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');
    if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour . '-bullet-colour');

    // Logo
    $logo = get_field('logo') ?: null;
    $logoLink = get_field('link') ?: null;
    $label = get_field('label') ?: 'Home';

    // Options
    $contentAlignment = get_field('content_alignment');
    if($contentAlignment) array_push($blockClasses, 'km-fb-content-' . $contentAlignment);
    $height = get_field('height') ?:  null;
    ?>
  
  
  <section class="<?php echo implode(' ', $blockClasses); ?>" <?php if($height) echo 'style="min-height:' . $height . '";'; ?>>
  
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

     <?php // Logo ?>
     <?php if($logo) : ?>
      <div class="km-fb-logo">
        <?php if($logoLink) : ?><a href="<?php echo $logoLink; ?>"> <?php endif; ?>
          <img src="<?php echo $logo; ?>" alt="<?php echo $label; ?>"/>
         <?php if($logoLink) : ?></a><?php endif; ?>
      </div>
     <?php endif; ?>

     <?php // CTAS ?>
      <?php if(!empty($ctas)) : ?>
        <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
          'links' => $ctas,
          'classPrefix' => 'km-fb', // optional
          'extraButtonClass' => 'cta-hover-x' // optional
        ] );
        ?>   
      <?php endif; ?>
      
    <div class="km-fb-wrap<?php if($extraClasses) echo ' ' . $extraClasses; ?>">


      <?php // CONTENT ?>
      <?php if($content) : ?>
        <div class="<?php echo implode(' ', $contentClasses); ?>">
        <div>
          <?php echo $content; ?>
        </div>
      </div>
      <?php endif; ?>
  
      <?php // Form ?>
      <?php if(get_field('block-form')) : ?>
        <?php get_template_part('template-parts/common/block-form/snippet','markup', [
          'form' => get_field('block-form'),
        ] );
        ?>   
      <?php endif; ?>
        
    </div>
  </section> 

    