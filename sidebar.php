<?php // required file since 3.0.0 ?>

<?php  if ( is_active_sidebar(get_post_type()) ) : ?>
  <aside id="km-<?php echo get_post_type();?>-sidebar" class="km-sidebar" role="complementary">
    <?php dynamic_sidebar(get_post_type()); ?>
  </aside>
<?php endif;  ?>