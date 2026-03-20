<?php

/*
 [recent_posts]
 [recent_posts post_type='case-study' count=1]

*/
  $props = is_array($atts) ? $atts : [];
  $count = array_key_exists('count', $props) ? $props['count'] : '3';
  $postType = array_key_exists('post_type', $props) ? $props['post_type'] : 'post';

  $args = [
    'post_type' => $postType,
    'posts_per_page' => $count,
    'post_status' => 'publish',
    'post__not_in' => array(get_the_ID())
  ];

  $recentPosts = new WP_Query($args);
?>

<ul class="km-recent-posts km-post-type-<?php echo $postType; ?>">
   <?php  while ($recentPosts->have_posts()) : ?>
   <?php $recentPosts->the_post(); ?>
    <li class="km-rp-item <?php if(!has_post_thumbnail()) echo 'km-rp-no-image'; ?>">
      <?php the_post_thumbnail('thumbnail'); ?>  
      <span class="km-rp-content">
        <a class="km-rp-title" href="<?php esc_url(the_permalink()); ?>">
          <?php esc_html(the_title()); ?>
        </a>
        <span class="km-rp-date"><?php esc_html(the_date()); ?></span>
      </span>
    </li>
    <?php 
    endwhile;
    wp_reset_postdata();
  ?>
</ul>