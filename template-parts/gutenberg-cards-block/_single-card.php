
<?php
  $card = $template_args['card'];

  if(!$card['visible']) return null;

  $title = $card['title'];
  $content = $card['content'];
  $image = $card['image'] ?: [];
  $imgUrl = array_key_exists('url', $image) ? $image['url'] : null;


  $bulletSlug = $card['bullet_colour']['picker']['slug'];


  $cardHeight = $card['card_height'] ?: $card['card_height_bulk'] ?: null;
  $cardWidth =  $card['card_width'] ?: $card['card_width_bulk'] ?: null;
  $ctas = $card['card_links'] ?: [];
  $imageSide = $card['image_side'] === true ? 'left' : 'right';
  $initialContent = $card['initial_content_type'] === true ? 'image' : 'content';

  // Classes
  $cardClasses = ['gcb-card','gcb-image-' . $imageSide];
  $titleClasses = ['gcbc-title'];
  $contentClasses = ['gcbc-content','km-wysiwyg'];

  // Colours 
  $cardBg = $card['card_bg']['picker']['slug'];
  $titleColour = $card['title_colour']['picker']['slug'];
  $contentColour = $card['content_colour']['picker']['slug'];
  $bulletColour = $card['bullet_colour']['picker']['slug'];
  $linkColour = $card['link_colour']['picker']['slug'];

  if($cardBg) array_push($cardClasses, 'has-' . $cardBg . '-background-colour');
  if($titleColour) array_push($titleClasses, 'has-' . $titleColour . '-colour');
  if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');

  if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour . '-bullet-colour');
  if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');


  if($initialContent === 'image') array_push($cardClasses, 'gcb-image-first');
  if($initialContent === 'content') array_push($cardClasses, 'gcb-content-first');
  if($imgUrl) array_push($cardClasses, 'gcb-has-image');

  // Image props
  $defaultBg = [
    'horizontal' => 'left',
    'vertical' => 'top',
    'background_repeat' => ['no-repeat', 'no-repeat'],
    'background_size' => 'auto'
  ];

  $bg = $card['image_configuration'] ?: $defaultBg;
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
    'background-repeat' => $repeatProp,
    'background-position' => $bg['horizontal'] . ' ' . $bg['vertical'],
    'background-size' => $bg['background_size']
  ];
  // If image is absolute
  //if($imgUrl) $imageProps['background-image'] = 'url(' . $imgUrl . ')';

  // Configure card height if set
  $cardProps = [];
  if($cardHeight) $cardProps['min-height'] = $cardHeight;
  if($cardWidth) $cardProps['min-width'] = $cardWidth;
?>


<div class="<?php echo implode(' ', $cardClasses); ?>"<?php if(!empty($cardProps)) echo populateStyleAttribute($cardProps); ?>>

    <?php // Image ?>
    <?php if($imgUrl) : ?>
      <div class="gcb-front" <?php echo populateStyleAttribute($imageProps); ?>>
        <img <?php //class="responsive-only" // if image is absolute ?> src="<?php echo $imgUrl; ?>" alt="<?php echo $image['alt'] ?: $title ?: 'Card Image'; ?>" />
      </div>
    <?php endif; ?>

    <?php if($title || $content) : ?>

      <div class="gcb-back">

        <?php // Title ?>
        <?php if($title) : ?>
          <h2 class="<?php echo implode(' ', $titleClasses); ?>"><?php echo $title; ?></h2>
        <?php endif; ?>

        <?php // Content ?>
        <?php if($content) : ?>
          <div class="<?php echo implode(' ', $contentClasses); ?>"><?php echo $content; ?></div>
        <?php endif; ?>

        <?php // CTAS ?>
        <?php if(count($ctas) > 0) : ?>
          <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
              'links' => $ctas,
              'classPrefix' => 'gcb', // optional
              'extraButtonClass' => 'cta-hover-x' // optional
            ] );
          ?>   
        <?php endif; ?>
    <?php endif; ?>

  </div>

</div>