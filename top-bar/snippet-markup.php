<?php
  $topBar = get_field('top_bar','option');
  $breakpoint = $topBar['breakpoint'] ?: 'false';
  $result = $topBar['alternative_state_on_scroll'] ? $breakpoint : 'false';
  $topBarButtons = get_field('top_bar_buttons','option');
?>

<style type="text/css">
  .main-menu-toggle.button-reset {
    display: none;
  }
</style>

<div class="top-bar" data-alt="<?php echo $result; ?>">
  <button class="push-menu-trigger" title="Show menu">Show menu</button>
  <?php
    hm_get_template_part( get_template_directory() . '/template-parts/main-menu/_trigger.php');  
    get_template_part('template-parts/main-header/snippet', 'markup');
    get_template_part('template-parts/contact-links/snippet', 'markup', [
    'location' => 'top',
    'classes' => ''
    ]); 

    get_template_part('template-parts/main-menu/snippet-markup');

    if(!empty($topBarButtons)){
      hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
        'links' => $topBarButtons['links'],
        'classPrefix' => 'tb', // optional
        'extraButtonClass' => 'cta-hover-x' // optional
      ] );
    }
    ?>
</div>


<?php if(!is_front_page()) : ?>
  <div class="top-bar-placeholder">&nbsp;</div>
<?php endif; ?>

<?php  get_template_part('template-parts/main-menu/push-menu'); ?>