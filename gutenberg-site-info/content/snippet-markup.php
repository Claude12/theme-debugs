<?php

  $data = $args['data'];
  $colours = $args['colours'];
  $content = $data['content'];
 ?>


<div class="km-si-content-wrap">

  <?php get_template_part('template-parts/gutenberg-site-info/title', null, ['data' => $data['title'] ]);?>

  <?php if($content) : ?>
    <div class="km-si-content<?php if($colours['content']) echo ' ' . $colours['content']; if($colours['links']) echo ' ' . $colours['links']; ?>"><?php echo $content; ?></div>
  <?php endif; ?>

</div>