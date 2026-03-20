 
<?php  
  $cardPosition = get_field('card_position') === true ? 'left' : 'right';
  $blockClasses = array_merge(['km-gutenberg-card-content','gcc-card-' . $cardPosition], $args['acf-classes']);
  $blockClasses =  createClasses($blockClasses, 'block_background', 'background-colour');
?>


<article class="<?php echo $blockClasses; ?>">
  <?php
    hm_get_template_part( get_template_directory() . '/template-parts/gutenberg-card-content/_card.php'); 
    hm_get_template_part( get_template_directory() . '/template-parts/gutenberg-card-content/_content.php'); 
  ?>
</article>
