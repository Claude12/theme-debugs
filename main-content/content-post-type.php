<?php while ( have_posts() ) : the_post(); ?>

 <?php if(!empty(get_the_content())) : ?>

  <?php 

    $blocks = parse_blocks( get_the_content() );
    $content = '';
    $exclude = ['acf/hero-carousel']; // sits outside either container

    foreach ( $blocks as $block ) {
      if ( !in_array($block['blockName'], $exclude)) {
        
        if($block['blockName'] !== null && !empty( trim( $block['innerHTML'] )) && strpos($block['blockName'], 'acf/') === false) {
          $content .= '<div class="km-core-content">' . render_block( $block ) . '</div>';
        }else {
          $content .=  render_block( $block );
        }
      }
    }

  ?>

  <div class="main-content-wrap<?php if(is_active_sidebar( get_post_type())) echo ' km-has-sidebar'; ?>">

   <?php // Watermark ?>

    <div class="km-watermark desktop-only km-watermark-top-right km-animation-enabled km-watermark-offset">
      <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/green-top-right.webp' ?>" alt="section watermark" />
    </div>


    <div class="main-content" id="main-content">

      <?php
        // the_content();
        $priority = has_filter( 'the_content', 'wpautop' );
        if ( false !== $priority ) remove_filter( 'the_content', 'wpautop', $priority );
        echo apply_filters( 'the_content', $content );
        if ( false !== $priority ) add_filter( 'the_content', 'wpautop', $priority );
      ?>

    </div>

     <?php echo get_template_part('sidebar'); ?>

    <div class="km-watermark desktop-only km-watermark-bottom-left">
      <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/green-bottom-left.webp' ?>" alt="section watermark" />
    </div>

  </div>

 <?php endif; endwhile; ?>
 
 <?php if(empty(get_the_content())) : ?>
  <div class="km-awaiting-content">
    <h2>Page is awaiting content</h2>
    <p>Please come back later...</p>
  </div>
 <?php endif; ?>