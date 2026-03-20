 
<?php  

  $intro = get_field('intro_text') ?: null;
  $title = get_field('title') ?: null;
  $titleSize = get_field('title_size') ? get_field('title_size')['heading'] : null;

  $content = get_field('content') ?: null;
  $ctas = get_field('buttons') ? get_field('buttons')['links'] ?: [] : [];

  // Classes
  $blockClasses = array_merge(['km-generic-block','km-has-bullet-slug'],$args['acf-classes']);
  $blockClasses = createClasses($blockClasses, 'background','background-colour');
  $introClasses = createClasses(['kgb-intro'], 'intro_colour');
  $titleClasses = ['kgb-title'];

  if($titleSize) array_push($titleClasses, $titleSize);

  $titleClasses = createClasses($titleClasses, 'title_colour');

  $initialContentClasses = ['kgb-content','km-wysiwyg'];
  $linkColour = get_field('link_colour')['picker']['slug'];
  $bulletColour = get_field('bullet_colour')['picker']['slug'];
  if($linkColour) array_push($initialContentClasses, 'has-' . $linkColour . '-link-colour');
  if($bulletColour) array_push($initialContentClasses, 'has-' . $bulletColour . '-bullet-colour');
  $contentClasses = createClasses($initialContentClasses, 'content_colour');

  // Config
  $contentCentered = get_field('content_centered');

  $wrapProps = [];
  $contentsWidth = get_field('contents_width') . 'px';
  if($contentsWidth) $wrapProps['max-width'] = $contentsWidth;

?>


<?php if($intro || $title || $content || count($ctas) > 0) : ?>
  <section class="<?php echo $blockClasses; ?>" >

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

  <div class="kgb-wrap <?php if($contentCentered) echo 'kgb-centered-content'; ?>" <?php echo populateStyleAttribute($wrapProps); ?>>
  
    <?php // Title ?>
    <?php if($title) : ?>
      <h2 class="<?php echo $titleClasses; ?>"><?php echo $title; ?></h2>
    <?php endif; ?>

    <?php // Intro ?>
    <?php if($intro) : ?>
      <div class="<?php echo $introClasses; ?>">
        <p class="heading-xxxx"><?php echo $intro; ?></p>
     </div>
    <?php endif; ?>

    <?php // Content ?>
    <?php if($content) : ?>
      <div class="<?php echo $contentClasses; ?>"><?php echo $content; ?></div>
    <?php endif; ?>

    <?php // CTAS ?>
    <?php if(count($ctas) > 0) : ?>
      <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
          'links' => $ctas,
          'classPrefix' => 'kgb', // optional
          'extraButtonClass' => 'cta-hover-x' // optional
        ] );
      ?>   
    <?php endif; ?>

  </div>


</section>
<?php endif; ?>

