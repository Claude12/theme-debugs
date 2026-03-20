<?php // Bar Menu

 // Add "km-x-tiers" to the menu if you require more than 1 dropdown level
 // Add "sub-menu-left" to the menu-item-has-children if dropdown needs to appear to the left of its parent
?>

<nav class="main-nav bar-menu km-x-tiers" id="bar-menu">
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

