<?php
  get_header();
  $postType = get_post_type() ?: null; 
?>

<div class="main-content-wrap">
  <div class="main-content" id="main-content">

    <?php
      if(!is_front_page() && is_home()) {
        $data = get_post(get_queried_object_id());
        get_template_part('parse','blocks',[
          'data' => $data->post_content,
          'render-html' => false
        ]);
      }
    ?>

    <?php if (have_posts()) : ?>

    <div class="km-post-type-feed <?php if($postType) echo 'km-' . $postType . '-feed'; ?>">
      <div class="km-ptf-wrap">
        <?php 
          while (have_posts()){
            the_post(); 
            get_template_part('template-parts/post-type-templates/' . $postType . '/single', 'markup');
          } 
        ?>
      </div>

      <?php endif; ?>
      <?php // https://developer.wordpress.org/reference/functions/get_the_posts_pagination/ ?>
      <?php
        $nav = get_the_posts_pagination([
          'class' => 'cpt-nav',
          'prev_text'          => __( '<span class="km-pt-nav-item km-pt-prev" title="%title"><svg><use href="#single-post-prev" /></svg></span>', 'twentyfifteen' ),
          'next_text'          => __( '<span class="km-pt-nav-item km-pt-next" title="%title"><svg><use href="#single-post-next" /></svg></span>', 'twentyfifteen' ),
          'screen_reader_text' => __( 'A' )
        ]);
        $nav = str_replace('<h2 class="screen-reader-text">A</h2>', '', $nav);
        echo $nav;
      ?>
    </div>
  </div>
</div>


<?php get_footer(); ?>