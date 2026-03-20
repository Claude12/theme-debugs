 <?php  
  $cards = get_field('cards') ?: [];
  $cardHeightBulk = get_field('card_height_bulk');
  $cardWidthBulk = get_field('card_width_bulk');

  if(empty($cards)) return;
?>

<article class="gutenberg-cards-block<?php if(!empty($args['acf-classes'])) echo ' ' . implode(' ',$args['acf-classes']); ?>">
  <div class="gcb-wrap">
    <?php foreach($cards as &$card) :?>
      <?php 
        $card['card_height_bulk'] = $cardHeightBulk;
        $card['card_width_bulk'] = $cardWidthBulk;
      ?>
      <?php hm_get_template_part( get_template_directory() . '/template-parts/gutenberg-cards-block/_single-card.php', [
        'card' => $card
      ]);  ?>
    <?php endforeach; unset($card); ?>
  </div>
</article>