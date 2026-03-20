
    <?php get_template_part('template-parts/scroll-to-top/snippet', 'markup'); ?>
    <?php get_template_part('template-parts/gutenberg-site-info/snippet', 'markup', ['global' => true]); ?>  
    <?php get_template_part('template-parts/main-footer/snippet', 'markup'); ?>
    <?php get_template_part('template-parts/unsupported-browser/snippet-markup'); ?>
    <?php get_template_part('template-parts/widget-modal-popout/snippet', 'markup'); ?> 
    <?php get_template_part('template-parts/cookie-notification/snippet', 'markup'); ?>
    <?php  //get_template_part('template-parts/cookie-notification-restricted/snippet', 'markup'); // Remove if non restrictive cookie bar is used ?> 


    <?php if(get_field('google_api_key','option')) : ?>
      <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_field('google_api_key','option'); ?>"></script>
    <?php endif; ?> 

    
    </div> <!-- /container -->
    <?php wp_footer(); ?>
  </body>
</html>
