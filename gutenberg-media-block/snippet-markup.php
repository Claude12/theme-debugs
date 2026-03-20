
<?php

  $layout = get_field('layout') ?: 'full-width';

  $preventStretch = get_field('prevent_stretch');
  $animateContent = get_field('animate_content');
  $content = get_field('content');
  $ctas = get_field('buttons') ? get_field('buttons')['links'] ?: [] : [];

  // Classes
  $blockClasses = array_merge(['km-media-block', 'km-mb-layout-' . $layout], $args['acf-classes']);
  $contentWrapClasses = ['km-mb-a'];
  $contentClasses = ['km-mb-content', 'km-wysiwyg'];

  // Colours
  $backgroundColour = get_field('bg_colour')['picker']['slug'];
  $contentBackgroundColour = get_field('content_background')['picker']['slug'];
  $contentColour = get_field('content_colour')['picker']['slug'];
  $linkColour = get_field('link_colour')['picker']['slug'];
  $bulletColour = get_field('bullet_colour')['picker']['slug'];

  if($preventStretch) array_push($blockClasses, 'km-mb-no-stretch');

  if($animateContent) array_push($blockClasses, 'km-mb-animated');
  if($backgroundColour) array_push($blockClasses, 'has-' . $backgroundColour . '-background-colour');
  if($contentBackgroundColour) array_push($contentWrapClasses, 'has-' . $contentBackgroundColour . '-background-colour');
  if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');
  if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');
  if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour . '-bullet-colour');

  // Options
  $contentLocation = get_field('content_location');
  if($contentLocation) array_push($blockClasses, 'km-mb-content-' . $contentLocation);

  $media = get_field('media');
  $mediaHeight = get_field('media_height') ?: null;
  $mediaSide = get_field('media_side') ?: 'left';


  $bProps = [];
  if($mediaHeight) $bProps['min-height'] = $mediaHeight . 'px';

  // Add class name if only one side is provided
  $isSingleItem = false;
  if(!$content && empty($ctas))  $isSingleItem = true;
  if(($content || !empty($ctas)) && empty(get_field('video')) && $media === 'video')  $isSingleItem = true;
  if(($content || !empty($ctas)) && empty(get_field('images')) && $media === 'gallery')  $isSingleItem = true;

  if($isSingleItem) array_push($blockClasses, 'km-mb-single');

?>
  
  <section class="<?php echo implode(' ', $blockClasses); ?>">

    <div class="km-mb-wrap<?php echo ' ' . 'km-mb-media-' . $mediaSide; ?>">

      <?php if($content || !empty($ctas)) : ?>
        <div class="<?php echo implode(' ', $contentWrapClasses); ?>">
          <?php // CONTENT ?>
        
              <div class="<?php echo implode(' ', $contentClasses); ?>">
          
                <?php echo $content; ?>
                <?php // CTAS ?>
                <?php if(!empty($ctas)) : ?>
                  <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
                    'links' => $ctas,
                    'classPrefix' => 'km-mb', // optional
                    'extraButtonClass' => 'cta-hover-x' // optional
                  ] );
                  ?>   
                <?php endif; ?>
            </div>
        </div>
      <?php endif; ?>

    <?php if(get_field('video') || !empty(get_field('images'))) : ?>
      <div class="km-mb-b km-media-<?php echo $media; ?>" <?php echo populateStyleAttribute($bProps); ?>>
      
        <?php if($media === 'video') : ?>
          <?php get_template_part('/template-parts/gutenberg-media-block/_video', null, [
            'video' => get_field('video') ?: null,
            'poster' => get_field('poster') ?: null,
            'video_options' => get_field('video_options'),
            'show_audio_control' => get_field('show_audio_control')
          ]); ?>
        <?php endif; ?>

        <?php if($media === 'gallery') : ?>
          <?php get_template_part('/template-parts/gutenberg-media-block/_gallery', null, [
            'images' => get_field('images') ?: [],
          ]); ?>
        <?php endif; ?>

      </div>
    <?php endif; ?>
        
    </div>
  </section> 

    