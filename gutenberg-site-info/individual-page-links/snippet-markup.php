<?php

  $data = $args['data'];
  $items = $data['individual_pages'];
  $colours = $args['colours'];

 ?>


<div class="km-si-content-wrap">

  <?php get_template_part('template-parts/gutenberg-site-info/title', null, ['data' => $data['title'] ]);?>
  <ul class="km-si-link-list<?php if($colours['content']) echo ' ' . $colours['content']; if($colours['links']) echo ' ' . $colours['links']; ?>">
    <?php foreach ($items as $item): ?>
      <li>
        <a class="km-si-link" href="<?php echo get_the_permalink($item); ?>"><?php echo $item->post_title; ?></a>
    </li>
    <?php endforeach; ?>
  </ul>

</div>