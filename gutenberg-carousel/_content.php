
<?php

  $data = $args['slide'];
  $buttons = $data['buttons'] ?: [];
  $content = $data['content'];
  $colours = $args['colours'];

  if(empty($buttons['links']) && !$content) return;

  $contentClasses = [];

  if($colours['colour']) array_push($contentClasses, 'has-' . $colours['colour'] . '-colour');
  if($colours['links']) array_push($contentClasses, 'has-' . $colours['links'] . '-link-colour');
  if($colours['bullets']) array_push($contentClasses, 'has-' . $colours['bullets'] . '-bullet-colour');

?>

<div class="km-carousel-content km-wysiwyg <?php if($colours['background']) echo 'has-' . $colours['background'] . '-background-colour'; ?>">

  <?php if($content) : ?>
    <div class="km-carousel-text <?php echo implode(' ', $contentClasses); ?>"><?php echo $content; ?></div>
  <?php endif; ?>

  <?php if(count($buttons['links'] ?: []) > 0) : ?>
  <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
      'links' => $buttons['links'],
      'classPrefix' => 'km-carousel', 
      'extraButtonClass' => 'cta-hover-x'
    ] );
  ?>   
  <?php endif; ?>

</div>


