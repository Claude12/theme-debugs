<?php
  get_header();
  if ( function_exists('yoast_breadcrumb') ) {
    yoast_breadcrumb( '<div class="yoast-breadcrumbs" style="padding-bottom: 30px;">','</div>' );
  }
?>

  <?php /* <div class="main-content-wrap">
    <div class="main-content">
      <p><?php //esc_html_e( 'Oops! That page can&rsquo;t be found.', 'km_unicorn' ); ?></p>
      <div class="ctas">
        <a class="cta" href="<?php //echo esc_url( home_url( '/' ) ); ?>" rel="Home" target="_self">Return to Home</a>
      </div>
    </div>
  </div> */ ?>

<?php
get_footer();