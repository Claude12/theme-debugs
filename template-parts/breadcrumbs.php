<?php
  /*
   <?php get_template_part('template-parts/breadcrumbs'); ?>
  */
  $enableCrumbs = get_field('show_breadcrumbs', 'option');

  if(!is_404() && $enableCrumbs) {
    if ( function_exists('yoast_breadcrumb') && !is_front_page() ) {
      yoast_breadcrumb( '<div class="km-breadcrumbs">','</div>' );
    }
  }
?>