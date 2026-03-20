<?php

  $author =  get_post_meta( get_the_ID(), 'testimonial-author', true );
  $position = get_post_meta( get_the_ID(), 'testimonial-author-title', true );

?>


<article class="km-testimonial-item">
  <p>"<?php echo the_content(); ?>"</p>
  <h3><?php echo $author; ?> - <?php echo $position; ?></h3>
</article>