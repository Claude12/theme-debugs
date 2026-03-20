 
<?php 
  $item = $args['item']; 
  $id = $item['id'];
  $identifier = $item['identifier'] ?: null; // This is post title. Not in use currently but handy to have
  $intro = $item['intro_title'] ?: null;
  $content = $item['content'] ?: null;
  $author = $item['author'] ?: null;
  $authorPos = $item['author_position'] ?: null;
  $iconLocation = get_field('icon_location');
  $contentLocation = get_field('content_location');
  $blockClasses = ['testimonial-block-item'];

  if($contentLocation) array_push($blockClasses, 'tbi-content-' . $contentLocation);

  // Colours
  $bracketClasses = createClasses(['km-bracket-wrap'], 'bracket_colour', 'background-colour');
  $initContentClasses = ['tbi-content','km-wysiwyg','secondary-font'];
  $bulletColour = get_field('bullet_colour')['picker']['slug'];
  $linkColour = get_field('link_colour')['picker']['slug'];

  if($linkColour) array_push($initContentClasses, 'has-' . $linkColour . '-link-colour');
  if($bulletColour) array_push($initContentClasses, 'has-' . $bulletColour . '-bullet-colour');
  $contentClasses = createClasses($initContentClasses, 'content_colour');

  $authorIntroClasses = createClasses(['tbi-intro'], 'author_intro_colour');
  $authorClasses = createClasses(['tbi-author'], 'author_colour');
  $authorPositionClasses = createClasses(['tbi-position'], 'author_position_colour');

?>

<div class="<?php echo implode(' ',  $blockClasses); ?>" data-item-id="<?php echo $id; ?>">

      <?php // ICON LEFT ?>
      <?php if($iconLocation === 'left' || $iconLocation === 'both') : ?>
        <?php get_template_part('/template-parts/gutenberg-testimonial-block/_icon', null, ['icon_classes' => createClasses(['km-tbi-icon-wrap','tbi-icon-left'], 'icon_colour', 'fill')]); ?>
      <?php endif; ?>

      <div class="tbi-content-wrap">

        <?php // CONTENT ?>
        <div class="<?php echo $contentClasses; ?>"><?php echo $content; ?></div>

        <?php // Intro ?>
        <?php if($intro) : ?>
          <span class="<?php echo $authorIntroClasses; ?>"><?php echo $intro; ?></span>
        <?php endif; ?>

        <?php // AUTHOR ?>
        <?php if($author) : ?>
          <span class="<?php echo $authorClasses; ?>"><?php echo $author; ?></span>
        <?php endif; ?>

        <?php // POSITION ?>
        <?php if($authorPos) : ?>
          <span class="<?php echo $authorPositionClasses; ?>"><?php echo $authorPos; ?></span>
        <?php endif; ?>

      </div>

      <?php // ICON RIGHT ?>
      <?php if($iconLocation === 'right' || $iconLocation === 'both') : ?>
        <?php get_template_part('/template-parts/gutenberg-testimonial-block/_icon', null, ['icon_classes' => createClasses(['km-tbi-icon-wrap','tbi-icon-right'], 'icon_colour', 'fill')]); ?>
      <?php endif; ?>


</div>


