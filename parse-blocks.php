<?php
/*

// For main pages
 <?php 
  get_template_part('parse','blocks',[
    'data' => get_the_content(),
    'render-html' => true
  ]);
 ?>

 // FOR other pages
  $data = get_post(get_queried_object_id());

  get_template_part('parse','blocks',[
    'data' => $data->post_content,
    'render-html' => false
  ]);

 */

 if(empty($args['data'])) return;

 $blocks = parse_blocks($args['data']);
 $content = '';
 $exclude = ['acf/hero-carousel'];

 foreach ( $blocks as $index => $block ) {

     // Exclude First carousel block from rendering within main-content
     if($index === 0 && $block['blockName'] === 'acf/carousel') {
      continue;
    }


   if ( !in_array($block['blockName'], $exclude)) {
     if($block['blockName'] !== null && !empty( trim( $block['innerHTML'] )) && strpos($block['blockName'], 'acf/') === false) {
       $content .= '<div class="km-core-content">' . render_block( $block ) . '</div>';
     }else {
       $content .=  render_block( $block );
     }
   }
 }
?>

<?php if($args['render-html']) : ?>
  <div class="main-content-wrap">
    <div class="main-content" id="main-content">
 <?php endif; ?>

    <?php
      $priority = has_filter( 'the_content', 'wpautop' );
      if ( false !== $priority ) remove_filter( 'the_content', 'wpautop', $priority );
      echo apply_filters( 'the_content', $content );
      if ( false !== $priority ) add_filter( 'the_content', 'wpautop', $priority );
    ?>

 <?php if($args['render-html']) : ?>
  </div>
 </div>
 <?php endif; ?>