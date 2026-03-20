<?php

  $data = $args['data'];
  $colours = $args['colours'];
  $parentId = $data['pages_from_parent'];
  $excludePages = $data['exclude_pages'] ?: [];

  $items = get_pages( [
    'child_of'    => $parentId,
    'parent'      => $parentId, // used to ensure only first tier
    'exclude'   => $excludePages,// exclude current page
    'sort_column' => 'menu_order',
    'order'       => 'ASC'
  ]);

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