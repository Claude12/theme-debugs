<?php 

  // Image
  $imageData = get_field('image') ? : [];
  $imgUrl = array_key_exists('url', $imageData) ? $imageData['url'] : null;
  $imgAlt = array_key_exists('alt', $imageData) ? $imageData['alt'] ?: $imageData['title'] : null;
  $renderImage = get_field('render_image') === true ? 1 : 0;
  $imgRight = get_field('image_location') === true ? 0 : 1;
  $contentRight = get_field('content_location') === true ? 0 : 1; // Works only if image is not enabled. Use image flag if it is enabled
  $imageMinHeight = get_field('img_min_height') ?: null;

  // Image background-position control
 
  $bg = get_field('image_props');
  $imageProps = [
    'background-image' => 'url(' . $imgUrl .')',
    'background-repeat' => $bg['bg_repeat'],
    'background-position' => $bg['bg_position'],
    'background-size' => $bg['bg_size']
  ];

  if($imageMinHeight) $imageProps['min-height'] = $imageMinHeight;

  $wrapClasses = ['scs-wrap'];
  if($imgRight && $renderImage) array_push($wrapClasses, 'scs-img-right');
  if($contentRight && !$renderImage) array_push($wrapClasses, 'scs-content-right');

  // Content
  $introTitle = get_field('intro_title');
  $title = get_field('title');
  $content = get_field('content');
  $cap = get_field('cap_content_width'); // this caps inner content within content section.

  // Dimensions
  $contentWidth = get_field('content_width');
  $contentProps = [];
  if($contentWidth) $contentProps['min-width'] = $contentWidth;


  $imageWidth = get_field('image_width');
  if($imageWidth) $imageProps['min-width'] = $imageWidth;

 
  $blockClasses = array_merge(['split-content-section'],$args['acf-classes']);

  // Section background
  $sectionBg = get_field('background')['background_block'];

  // Colours
  $introClasses = createClasses(['scs-intro'], 'intro_colour');
  $titleClasses = createClasses(['scs-title'], 'title_colour');

  $initalContentClasses = ['scs-content','km-wysiwyg'];

  $bulletColour = get_field('bullet_colour')['picker']['slug'];
  $linkColour = get_field('link_colour')['picker']['slug'];


  if($bulletColour) array_push($initalContentClasses, 'has-' . $bulletColour . '-bullet-colour');
  if($linkColour) array_push($initalContentClasses, 'has-' . $linkColour . '-link-colour');

  $contentClasses = createClasses($initalContentClasses, 'content_colour');
  $contentBg = get_field('content_bg')['picker']['slug'];
?>

<section class="<?php echo implode(' ', $blockClasses); ?>">

  <?php // Section background ?>
  <?php if($sectionBg['background']) : ?>
    <?php hm_get_template_part( get_template_directory() . '/template-parts/common/background-block/background-block.php', ['background' => $sectionBg['background_configuration']] );?>   
  <?php endif; ?>

  <div class="<?php echo implode(' ', $wrapClasses); ?>">

    <?php // Image ?>
    <?php if($imgUrl && $renderImage) : ?>
      <div class="scs-image" <?php if(!empty($imageProps)) echo populateStyleAttribute($imageProps); ?>>
        <img class="responsive-only" src="<?php echo $imgUrl; ?>" alt="<?php echo $imgAlt; ?>">
      </div>
    <?php endif; ?>

      <div class="scs-content-wrap<?php if ($contentBg) echo ' has-' . $contentBg . '-background-colour'; ?>" <?php if(!empty($contentProps)) echo populateStyleAttribute($contentProps); ?>>
        <div class="scs-capped-width" <?php if($cap) echo 'style="max-width:'. $cap .'"' ?>>
          <?php // Intro ?>
          <?php if($introTitle) : ?>
            <div class="<?php echo $introClasses; ?>">
              <p><?php echo $introTitle; ?></p>
          </div>
          <?php endif; ?>

          <?php // Title ?>
          <?php if($title) : ?>
            <h2 class="<?php echo $titleClasses; ?>"><?php echo $title; ?></h2>
          <?php endif; ?>

          <?php // Content ?>
          <?php if($content) : ?>
            <div class="<?php echo $contentClasses; ?>"><?php echo $content; ?></div>
          <?php endif; ?>


          <?php // CTAS ?>
          <?php if(count(get_field('split_content')['links'] ?: []) > 0) : ?>
            <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
                'links' => get_field('split_content')['links'],
                'classPrefix' => 'scs', // optional
                'extraButtonClass' => 'cta-hover-x' // optional
              ] );
            ?>   
          <?php endif; ?>
        </div>
    </div>
  </div>
</section>