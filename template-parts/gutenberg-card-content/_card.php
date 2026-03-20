<?php


 // Content
 $cardImage = get_field('card_image') ?: [];
 $cardAlt = array_key_exists('alt', $cardImage) ? $cardImage['alt'] : 'Image';
 $cardUrl = array_key_exists('url', $cardImage) ? $cardImage['url'] : null;
 $cardTitle = get_field('card_title') ?: null;
 $cardContent = get_field('card_content') ?: null;

 $video = get_field('video_url');
 $poster = get_field('poster');

 // Settings
 $cardWidth = get_field('card_width') ?: null;
 $imageHeight = get_field('image_height') ?: null;
 
 // Image props
 $defaultBg = [
   'horizontal' => 'left',
   'vertical' => 'top',
   'background_repeat' => 'no-repeat',
   'background_size' => 'auto'
 ];

 $bg = get_field('image_configuration') ?: $defaultBg;

 $bgPos =  implode(' ',[$bg['horizontal'], $bg['vertical']]);
 $repeatProp = 'no-repeat';

 $bgProp = is_array($bg['background_repeat']) ? $bg['background_repeat'] : [];

 switch ($bgProp) {
   case 1:
     $repeatProp = 'repeat-' . $bg['background_repeat'][0];
     break;
   case 2:
     $repeatProp = 'repeat';
     break;
   default:
     $repeatProp = 'no-repeat';
 }

 $imageProps = [
   'background-image' => 'url(' . $cardUrl . ')',
   'background-repeat' => $repeatProp,
   'background-position' => $bg['horizontal'] . ' ' . $bg['vertical'],
   'background-size' => $bg['background_size']
 ];

  if( $imageHeight ) $imageProps['height'] = $imageHeight;

  $cardProps = [];
  if( $cardWidth ) $cardProps['max-width'] = $cardWidth;


  // Colours
   $cardClasses = createClasses(['gcc-card'], 'card_background', 'background-colour');
   $titleSet = ['gccc-title'];
   $titleClasses = createClasses($titleSet , 'title_colour');

   $initContentClasses = ['gccc-content','km-wysiwyg','gcc-part'];
   $bulletColour = get_field('bullet_colour')['picker']['slug'];
   $linkColour = get_field('card_link_colour')['picker']['slug'];

   if($bulletColour) array_push($initContentClasses, 'has-' . $bulletColour . '-bullet-colour');
   if($linkColour) array_push($initContentClasses, 'has-' . $linkColour . '-link-colour');

   $contentClasses = createClasses($initContentClasses, 'content_colour');
?>


<div class="<?php echo $cardClasses; ?>" <?php if(!empty($cardProps)) echo populateStyleAttribute($cardProps); ?>>

  <?php // Card Image ?>
  <?php if($cardUrl) : ?>
    <div class="gccc-image" <?php echo populateStyleAttribute($imageProps); ?>>
      <img class="responsive-only" src="<?php echo $cardUrl; ?>" alt="<?php echo $cardAlt; ?>" />
    </div>
  <?php endif; ?>

  <?php // Video ?>
  <?php if($video && $poster) : ?>
    <div class="gccc-video-wrapper">
      <video class="video-block lazy-bg-video" preload="none" stop-autoplay playsinline controls poster="<?php echo $poster; ?>"> 
        <source data-src="<?php echo $video; ?>" type="video/mp4">  
        <p>Your browser does not support this video.</p> 
      </video>
    </div>
  <?php endif; ?>
  
  <?php // single line because :empty CSS is in use ?>
  <div class="gccc-wrap"><?php if($cardTitle) : ?><h2 class="<?php echo $titleClasses; ?>"><?php echo $cardTitle; ?></h2><?php endif; ?><?php if($cardContent) : ?><div class="<?php echo $contentClasses; ?>"><?php echo $cardContent; ?></div><?php endif; ?></div>

</div>