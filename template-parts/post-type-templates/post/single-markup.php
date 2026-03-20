<?php 

  $data = array_key_exists('post', $args);
  $src = array_key_exists('post', $args) ? $args['post'] : null;
  $defaultImage = get_field('hero_banner')['slides'][0]['image']['url'] ?: get_field('default_blog_image','option')['url'];

  $id = $data ? $src->ID : get_the_ID();
  $title = $data ? $src->post_title : get_the_title();
  $link = $data ? get_the_permalink($id) : get_the_permalink();
  $featuredImg = $data ? get_the_post_thumbnail_url($id) ?: $defaultImage : get_the_post_thumbnail_url() ?: $defaultImage;
  $content = $data ? $src->post_excerpt : get_the_excerpt();

?>

<a class="km-blog-card" href="<?php echo $link; ?>">
  
  <?php // Featured image ?>
  <?php if($featuredImg): ?>
      <img class="kbc-image" src="<?php echo $featuredImg; ?>" alt="<?php echo $title; ?>" />
  <?php endif; ?>

  <div class="kbc-content">

    <?php // Title ?>
    <?php if($title): ?>
      <p class="kbc-title"><?php echo $title; ?></p>
    <?php endif; ?>

    <?php // Content ?>
    <?php if($content): ?>
      <p class="kbc-text"><?php echo $content; ?>...</p>
    <?php endif; ?>
  </div>

  <?php // Learn More ?>
  <?php if($link) : ?>
    <span class="km-post-read-more">
      <span>Read More</span>
    </span>
  <?php endif; ?>

</a>
