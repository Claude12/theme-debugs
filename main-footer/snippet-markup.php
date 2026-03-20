<footer class="main-footer">
  <div class="main-footer-wrap">
    <?php get_template_part('template-parts/contact-links/snippet', 'markup', [
        'location' => 'bottom',
        'classes' => 'footer-quick-links'
      ]); ?>

    <?php
        wp_nav_menu(
          array(
            'theme_location' => 'menu-2',
            'menu_id' => 'footer-wrap',
            'container' => '',
            'menu_class' => 'km-footer-list',
            'depth' => 1 
          )
        );
      ?>

      <?php
        wp_nav_menu(
          array(
            'theme_location' => 'menu-3',
            'menu_id' => 'footer-wrap-2',
            'container' => '',
            'menu_class' => 'km-footer-list',
            'depth' => 1 
          )
        );
      ?>

<!--       <ul class="km-ketchup-links km-footer-list">
        <li class="ketchup-link"><a target="_blank" rel="noopener" href="//www.ketchup-marketing.co.uk">Made better with <strong>Ketchup</strong></a></li>
      </ul> -->

      <?php get_template_part('template-parts/footer-logos/snippet', 'markup');  ?>
  </div>
</footer>


