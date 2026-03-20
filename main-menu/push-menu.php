<?php // Push Menu ?>

<div class="push-menu-wrap">

  <nav class="main-nav push-menu">
  <h2 class="sr-only">Main Menu</h2>
    <?php
    wp_nav_menu(
      array(
      'theme_location' => 'menu-1',
      'menu_id' => 'primary-menu',
      'container' => 'main-nav',
      'menu_class' => '',
      'depth' => 0 // or levels you want
      )
    );
    ?>
  </nav>

  <div class="push-menu-info">
    <?php
      $topBarButtons = get_field('top_bar_buttons','option');
      get_template_part('template-parts/contact-links/snippet', 'markup', [
        'location' => 'top'
        ]); 

        if(!empty($topBarButtons)){
          hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
            'links' => $topBarButtons['links'],
            'classPrefix' => 'tb', // optional
            'extraButtonClass' => 'cta-hover-x' // optional
          ] );
        }
    ?>
  </div>

</div>